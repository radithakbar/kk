<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Imports\BarangImport;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;


class BarangResource extends Resource
{
    

    protected static ?string $model = Barang::class;

    protected static ?string $navigationGroup =  'Inventaris';
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('kode_barang')
    
                        ->label('Kode Barang')
                        ->default(function () {
                            $lastKodeBarang = Barang::orderBy('kode_barang', 'desc')->first();
                    
                            if ($lastKodeBarang) {
                                // Ambil angka terakhir setelah strip
                                $parts = explode('-', $lastKodeBarang->kode_barang);
                                $lastNumber = isset($parts[1]) ? (int) $parts[1] : 0;
                                $newKodeBarang = $lastNumber + 1;
                            } else {
                                // Jika belum ada kode, mulai dari 1
                                $newKodeBarang = 1;
                            }
                    
                            // Default awalan huruf: "XXX", pengguna bisa mengganti ini
                            return 'XXX-' . str_pad($newKodeBarang, 4, '0', STR_PAD_LEFT);
                        })
                        ->required()
                        ->regex('/^[A-Z]{3}-\d{4}$/') // Validasi format 3 huruf dan 4 angka
                        ->placeholder('XXX-0001') // Placeholder untuk membantu pengguna
                        ->helperText('Format: 3 huruf diikuti tanda "-" dan 4 angka (contoh: ABC-0001)'),
                    
                        
                        Forms\Components\FileUpload::make('gambar')
                            ->label('Gambar')
                            ->acceptedFileTypes(['image/*']),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Barang')
                                    ->placeholder('Nama Barang')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->placeholder('Masuk jumlah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                
                                Forms\Components\Select::make('kondisi')
                                    ->label('Kondisi')
                                    ->options([
                                        1 => 'Baik',
                                        2 => 'Rusak',
                                    ]),
                            ]),

                        Forms\Components\TextArea::make('merek')
                            ->label('Merek')
                            ->placeholder('Merek')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')->label('Kode Barang')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable()->label('Nama Barang')->searchable(),
                Tables\Columns\TextColumn::make('merek')->label('Merek')->searchable(),
                Tables\Columns\TextColumn::make('qty')->label('Quantity')->sortable()->searchable()->numeric(),
                Tables\Columns\TextColumn::make('kondisi')->label('Kondisi')->searchable(),
                // ->getStateUsing(function ($record) {
                    //     return $record->kondisi == 1 ? 'Baik' : 'Rusak';
                    // })->searchable(),
                Tables\Columns\ImageColumn::make('gambar')->label('Gambar'),
            ])
        //     ->headerActions([
        //         ActionGroup::make([
        //             Tables\Actions\ButtonAction::make('Export to Excel')
        //                 ->action(function () {
        //                     $fileName = 'data_barang.xlsx'; // File name for the export

        //                     // Export data Barang
        //                     return (new FastExcel(Barang::all()))->download($fileName, function ($barang) {
        //                         return [
        //                             'Kode Barang' => $barang->kode_barang,
        //                             'Nama Barang' => $barang->nama,
        //                             'Merek' => $barang->merek,
        //                             'Kondisi' => $barang->kondisi == 1 ? 'Baik' : 'Rusak',
        //                             'Qty' => $barang->qty,
                                    
        //                         ];
        //                     });
        //                 })
        //                 ->label('Export to Excel'),
        //         ]),
        //         Tables\Actions\Action::make('Import Barang')
        //         ->form([
        //             FileUpload::make('file')
        //                 ->label('Pilih File')
        //                 ->disk('local') // Sesuaikan disk jika menggunakan storage lain
        //                 ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
        //                 ->required(),
        //         ])
        //         ->action(function (array $data) {
        //             $filePath = $data['file']; // Path file yang diunggah
        
        //             try {
        //                 // Proses impor data menggunakan FastExcel
        //                 Excel::import(new BarangImport(), Storage::path($filePath));

        
        //                 // Hapus file setelah selesai (opsional)
        //                 Storage::delete($filePath);
        
        //                 // Notifikasi sukses
        //                 Notification::make()
        //                     ->title('Sukses!')
        //                     ->success()
        //                     ->body('Data berhasil diimpor!')
        //                     ->send();
        //             } catch (\Exception $e) {
        //                 Notification::make()
        //                     ->title('Gagal')
        //                     ->danger()
        //                     ->body('Gagal mengimpor data: ' . $e->getMessage())
        //                     ->send();
        //             }
        //         })
        //         ->label('Import Barang')
        //         ,
        // ])
            
            ->actions([
                EditAction::make(),
                ViewAction::make(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
