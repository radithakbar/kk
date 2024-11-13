<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarisResource\Pages;
use App\Models\Inventaris;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;

class InventarisResource extends Resource
{
    protected static ?string $model = Inventaris::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')->label('Kode Barang')->searchable(),
                Tables\Columns\TextColumn::make('nama_barang')->label('Nama Barang')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('merk')->label('Merk')->sortable(),
                Tables\Columns\TextColumn::make('qty')->label('Qty')->sortable(),
                Tables\Columns\TextColumn::make('harga_pasaran')->label('Harga Pasaran')->money('IDR', true),
                Tables\Columns\TextColumn::make('jumlah')->label('Jumlah')->alignCenter(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->headerActions([
                ActionGroup::make([
                    Tables\Actions\ButtonAction::make('Export to Excel')
                        ->action(function () {
                            $fileName = 'data_inventaris.xlsx';
    
                            // Export data Inventaris
                            return (new FastExcel(Inventaris::all()))->download($fileName, function ($inventaris) {
                                return [
                                    'Kode Barang' => $inventaris->kode_barang,
                                    'Nama Barang' => $inventaris->nama_barang,
                                    'Merk' => $inventaris->merk,
                                    'Qty' => $inventaris->qty,
                                    'Harga Pasaran' => 'Rp ' . number_format($inventaris->harga_pasaran, 0, ',', '.'),
                                    'Jumlah' => $inventaris->jumlah,
                                    'Status' => $inventaris->status,
                                ];
                            });
                        })
                        ->label('Export to Excel'),

                    Tables\Actions\Action::make('Import Data')
                        ->label('Import Data')
                        ->action('importData') // Menunjuk pada metode statis
                        ->form([
                            FileUpload::make('file')
                                ->label('Pilih File Excel')
                                ->required()
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->disk('local')
                                ->directory('temp-uploads')
                        ])
                        ->button()
                        ->color('primary')
                ])->label('Data Actions')->outlined()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Baik' => 'Baik',
                        'Kurang Baik' => 'Kurang Baik',
                        'Rusak Berat' => 'Rusak Berat',
                    ])
                    ->label('Filter by Status')
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat'),
                    Tables\Actions\EditAction::make()->label('Ubah'),
                    Tables\Actions\DeleteAction::make()->label('Hapus'),
                ])->button()->label('Actions')->outlined(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Definisikan relasi jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventaris::route('/'),
            'create' => Pages\CreateInventaris::route('/create'),
            'edit' => Pages\EditInventaris::route('/{record}/edit'),
        ];
    }

   
}
