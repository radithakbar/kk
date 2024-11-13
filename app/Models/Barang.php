<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode_barang',
        'gambar',
        'nama',
        'qty',
        'merek',
        'kondisi',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    

}
