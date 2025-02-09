<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource\RelationManagers;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Barang;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use Filament\Notifications\Notification;







class PeminjamanResource extends Resource
{
    public static function getPluralLabel(): string
    {
        return 'Peminjaman'; 
    }
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    //card
                    Forms\Components\Card::make()
                    ->schema([
                        
                //barang
                Forms\Components\Select::make('barang_id')
                ->label('Nama Barang')
                ->relationship('barang', 'nama')  // Menampilkan nama barang dari relasi
                ->reactive()  // Agar data berubah setelah memilih barang
                ->required(),
                    
                // //gambar      
                // Forms\Components\FileUpload::make('gambar')
                
                // ->label('Gambar Barang')
                // ->disk('public')
                // ->image(),
                
                // Nama Siswa
                Forms\Components\TextInput::make('nama_siswa')
                ->required(),

                Forms\Components\TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->required()
                ->reactive()
                // ->afterStateUpdated(function ($state, callable $get, callable $set) {
                //     $barang = Barang::find($get('barang_id'));
                //     if ($barang && $state > $barang->qty) {
                //         $set('jumlah', $barang->qty); // Batasi ke jumlah maksimal stok
                // Notification::make()
                // ->title('Stok Tidak Cukup')
                // ->body("Jumlah yang dimasukkan melebihi stok yang tersedia. Maksimal: {$barang->qty}")
                // ->danger()
                // ->send();
        //  } )
     
                ]),
        
                    //grid
                    Forms\Components\Grid::make()
                    ->schema([
        
        
        
                    //tanggal peminjaman
                    Forms\Components\DatePicker::make('tanggal_peminjaman')
                        ->default(now())
                        ->required()
                        ->format('Y-m-d'),
                        
                    //tanggal pengembalian
                    Forms\Components\DatePicker::make('tanggal_pengembalian')
                        ->format('Y-m-d')
                        ->required(),
        
                    //keterangan
                    Forms\Components\Select::make('keterangan')
                    ->label('Keterangan')
                        ->options([
                            1 => 'Baik',
                            2 => 'Rusak',
                        ])
                        ->required(),
                        

                      //gambar      
                    Forms\Components\FileUpload::make('gambar')
                
                     ->label('Bukti foto')
                     ->disk('public')
                     ->image()
                     ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable()
                    ->searchable(),
        
                    Tables\Columns\TextColumn::make('barang.nama')
                        ->label('Nama Barang')
                        ->searchable()
                        ->searchable(),
                        
                    Tables\Columns\TextColumn::make('tanggal_peminjaman')
                        ->label('Tanggal Peminjaman')
                        ->sortable()
                        ->date('d M Y')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('tanggal_pengembalian')
                        ->label('Tanggal Pengembalian')
                        ->sortable()
                        ->date('d M Y')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('jumlah')
                        ->label('Jumlah')->searchable(),

                    Tables\Columns\TextColumn::make('keterangan') 
                        ->label('Keterangan')
                        ->getStateUsing(function ($record) {
                         return $record->keterangan == 1 ? 'Baik' : 'Rusak';
                        })
                        ->searchable(),

                        Tables\Columns\BadgeColumn::make('status')
                         ->label('Status')
                         ->colors([
                          'danger' => 'belum_dikembalikan',
                          'success' => 'dikembalikan',
                          ])
                          ->formatStateUsing(fn ($state) => $state === 'belum_dikembalikan' ? 'Belum Dikembalikan' : 'Dikembalikan')
                          ->sortable()
                          ->searchable(),

                    Tables\Columns\ImageColumn::make('gambar')
                     ->label('Bukti Foto')
                     ->disk('public')
                     ->sortable(),

                    

            ])

            
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat'),
                    Tables\Actions\EditAction::make()->label('Ubah'),
                    Tables\Actions\DeleteAction::make()->label('Hapus'),
                ])->button()->label('Actions')->outlined(),

                    Tables\Actions\Action::make('toggle_pengembalian')
                        ->label(fn (Peminjaman $record) => $record->status === 'belum_dikembalikan' ? 'Sudah Dikembalikan' : 'Belum Dikembalikan')
                        ->action(fn (Peminjaman $record) => static::togglePengembalian($record))
                        ->color(fn (Peminjaman $record) => $record->status === 'belum_dikembalikan' ? 'success' : 'danger')
                        ->requiresConfirmation()                       
                        ->button()
                        ->outlined(),
                
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function togglePengembalian(Peminjaman $peminjaman)
{
    DB::transaction(function () use ($peminjaman) {
        if ($peminjaman->status === 'belum_dikembalikan') {
            // Jika status 'belum_dikembalikan', ubah ke 'dikembalikan' dan buat data pengembalian
            $peminjaman->update(['status' => 'dikembalikan']);

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->barang->nama,
                'nama_siswa' => $peminjaman->nama_siswa,
                'tanggal_pengembalian' => now(),
                'keterangan' => $peminjaman->keterangan,
                'jumlah' => $peminjaman->jumlah,
                'gambar' => $peminjaman->gambar,
                'status' => $peminjaman->status,
            ]);
        } else {
            // Jika status 'dikembalikan', ubah ke 'belum_dikembalikan' dan hapus data pengembalian
            $peminjaman->update(['status' => 'belum_dikembalikan']);

            Pengembalian::where('peminjaman_id', $peminjaman->id)->delete();
        }
    });
}

//     public static function prosesPengembalian(Peminjaman $peminjaman)
// {
//     DB::transaction(function () use ($peminjaman) {
//         // Menyimpan data di tabel pengembalian
//         Pengembalian::create([
//             'peminjaman_id' => $peminjaman->id,
//             'nama_siswa' => $peminjaman->nama_siswa,
//             'tanggal_pengembalian' => now(),
//             'keterangan' => $peminjaman->keterangan,
//             'jumlah' => $peminjaman->jumlah,
//             'gambar' => $peminjaman->gambar,
//         ]);

//     });
// }


    // public static function prosesPengembalian(Peminjaman $peminjaman)
    // {

    //     // Membuat record di tabel pengembalian dengan data terkait dari peminjaman
    //     Pengembalian::create([
    //         'peminjaman_id' => $peminjaman->id, // ID peminjaman
    //         'nama_siswa' => $peminjaman->nama_siswa, // Nama siswa dari data peminjaman
    //         'tanggal_pengembalian' => now(), // Tanggal pengembalian saat ini
    //         'keterangan' => 'Barang dikembalikan', // Keterangan, bisa disesuaikan
    //         'jumlah' => $peminjaman->jumlah, // Jumlah barang yang dipinjam
    //         'gambar' => $peminjaman->gambar, // Gambar barang dari data peminjaman
    //     ]);
    // }
    


// public static function prosesPengembalian(Peminjaman $peminjaman)
// {
//     DB::transaction(function () use ($peminjaman) {
//         Log::info("Mulai menyimpan data ke tabel pengembalian.");
        
//         Pengembalian::create([
//             'peminjaman_id' => $peminjaman->id,
//             'nama_siswa' => $peminjaman->nama_siswa,
//             'tanggal_pengembalian' => now(),
//             'keterangan' => 'Barang dikembalikan',
//             'jumlah' => $peminjaman->jumlah,
//             'gambar' => $peminjaman->gambar,
//         ]);

//         Log::info("Data berhasil disimpan di pengembalian, akan menghapus data di peminjaman.");

//         $peminjaman->delete();

//         Log::info("Data di peminjaman berhasil dihapus.");
//     });
// }

    
   
}
