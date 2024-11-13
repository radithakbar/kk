<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 


class Pengembalian extends Model
{
    use HasFactory;
    protected $fillable = [
        'peminjaman_id',
        'nama_siwa',
        'Tanggal_pengembalian',
        'keterangan',
        'jumlah',
        'gambar',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class,'peminjaman_id');
    }   
}
