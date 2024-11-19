<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use App\Imports\BarangImport;
use App\Models\Barang;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Actions\ActionGroup::make([
                Actions\Action::make('exportBarang')
                    ->label('Export to Excel')
                    ->action(function () {
                        $fileName = 'data_barang.xlsx'; // Nama file untuk eksport

                        // Mengekspor data Barang ke Excel
                        return (new FastExcel(Barang::all()))->download($fileName, function ($barang) {
                            return [
                                'Kode Barang' => $barang->kode_barang,
                                'Nama Barang' => $barang->nama,
                                'Merek' => $barang->merek,
                                'Kondisi' => $barang->kondisi == 1 ? 'Baik' : 'Rusak',
                                'Qty' => $barang->qty,
                            ];
                        });
                    }),
            ]),
            Actions\Action::make('importBarang')
                ->label('Impor Data Barang')
                ->requiresConfirmation()
                ->modalHeading('Impor Data Barang')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('Pilih File Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel', 
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ])
                        ->directory('uploads')
                        ->preserveFilenames(),
                ])
                ->action(function (array $data) {
                    if (empty($data['file'])) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('File tidak ditemukan!')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $filePath = Storage::disk('public')->path('uploads/' . basename($data['file']));
                        Excel::import(new BarangImport, $filePath);

                        Notification::make()
                            ->title('Sukses')
                            ->body('Data barang berhasil diimpor!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Gagal mengimpor data: ' . $e->getMessage());
                        Notification::make()
                            ->title('Gagal')
                            ->body('Gagal mengimpor data barang: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                Actions\CreateAction::make()
            ->label('Barang Baru'),
         ];
    }
}
