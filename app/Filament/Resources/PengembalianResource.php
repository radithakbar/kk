<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Filament\Resources\PengembalianResource\RelationManagers;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



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
    return false;
}

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
         
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
                        ->default(now())
                        ->date()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('jumlah')
                        ->label('Jumlah'),

                    Tables\Columns\TextColumn::make('keterangan') 
                        ->label('Keterangan'),

                    Tables\Columns\ImageColumn::make('gambar')
                    ->label('Bukti Foto')
                    ->disk('public')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
               
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
            'index' => Pages\ListPengembalians::route('/'),
           
           
        ];
    }

   
}
