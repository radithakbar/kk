<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;






class BarangResource extends Resource
{
    public static function getPluralLabel(): string
    {
        return 'Data Barang'; // Nama plural tanpa "s"
    }
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 //card
            Forms\Components\Card::make()
            ->schema([

                //kode barang
                Forms\Components\TextInput::make('kode_barang')
                ->label('Kode Barang')
                ->default(function () {
                    $lastKodeBarang = Barang::max('kode_barang'); // Dapatkan kode barang terakhir
                    $newKodeBarang = $lastKodeBarang ? $lastKodeBarang + 1 : 1;
                    return str_pad($newKodeBarang, 4, '0', STR_PAD_LEFT); })
                ->unique()
                ->required(),
            
                //gambar
                Forms\Components\FileUpload::make('gambar')
                    ->label('Gambar')
                    ->acceptedFileTypes(['image/*']),

                //grid
                Forms\Components\Grid::make(3)
                  ->schema([

                     //nama
                      Forms\Components\TextInput::make('nama')
                      ->label('Nama Barang')
                      ->placeholder('Nama Barang')
                      ->required(),     

                     //jumlah
                     Forms\Components\TextInput::make('qty')
                     ->label('Quantity')
                     ->placeholder('Masuk jumlah')
                     ->required()
                     ->numeric() // Menentukan bahwa input harus berupa angka
                     ->minValue(1),// Optional: Menetapkan nilai minimum, misalnya tidak boleh negatif
                 
                     //kondisi
                     Forms\Components\Select::make('kondisi')
                     ->label('Kondisi')
                     ->options([
                     1 => 'Baik',
                     2 => 'Rusak',
            ])
                  ]),

                //merek
                Forms\Components\TextArea::make('merek')
                    ->label('Merek')
                    ->placeholder('Merek')
                    ->required(),

                // //tgl_register
                // Forms\Components\DatePicker::make('tgl_register')
                // ->label('tanggal_register')
                // ->default(now())
                // ->required(),

            ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang'),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('merek'),
                Tables\Columns\TextColumn::make('kondisi') ->label('Kondisi')
                 ->getStateUsing(function ($record) {
                        return $record->keterangan == 1 ? 'Baik' : 'Rusak';
                    }),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('gambar'),
            ])
        
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            //
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
