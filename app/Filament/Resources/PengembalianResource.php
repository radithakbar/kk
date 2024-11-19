<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Models\Pengembalian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Rap2hpoutre\FastExcel\FastExcel;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class PengembalianResource extends Resource
{
    public static function getPluralLabel(): string
    {
        return 'Pengembalian'; // Nama plural tanpa "s"
    }

    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false; // Disable creation if needed
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Define the form fields here if needed
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peminjaman.barang.nama')
                ->label('Nama Barang')
                ->sortable()
                ->searchable(),
                    
                Tables\Columns\TextColumn::make('peminjaman.nama_siswa')
                ->label('Nama Siswa')
                ->sortable()
                ->searchable(),
        
                Tables\Columns\TextColumn::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah'),

                    Tables\Columns\TextColumn::make('peminjaman.keterangan')
                    ->label('Keterangan')
                    ->getStateUsing(fn ($record) => $record->peminjaman->keterangan == 1 ? 'Baik' : 'Rusak')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('peminjaman.gambar')
                    ->label('Bukti Foto')
                    ->disk('public')
                    ->sortable(),
            ])
            ->headerActions([
                ActionGroup::make([
                    Tables\Actions\ButtonAction::make('Export to Excel')
                        ->action(function () {
                            $fileName = 'data_pengembalian.xlsx'; // File name for the export
    
                            // Export data Pengembalian
                            return (new FastExcel(Pengembalian::all()))->download($fileName, function ($pengembalian) {
                                $peminjaman = $pengembalian->peminjaman; // Get the associated peminjaman
    
                                return [
                                    'Nama Barang' => $peminjaman ? $peminjaman->barang->nama : 'N/A', // Check if peminjaman exists
                                    'Nama Siswa' => $pengembalian->nama_siswa,
                                    'Tanggal Pengembalian' => $pengembalian->tanggal_pengembalian,
                                    'Jumlah' => $pengembalian->jumlah,
                                    'Keterangan' => $pengembalian->keterangan,
                                    // Optional: If you want to include 'gambar', you can add it like this
                                    // 'Bukti Foto' => $pengembalian->gambar,
                                ];
                            });
                        })
                        ->label('Export to Excel'),
                ]),
            ])
            ->filters([
                // Define any filters if needed
            ])
            ->actions([
                // Define any action buttons if needed
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengembalians::route('/'),
        ];
    }
}