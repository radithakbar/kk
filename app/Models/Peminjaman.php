<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; 


class Peminjaman extends Model
{
    use HasFactory;
    protected $fillable = [
        'barang_id',
        'nama_siswa', 
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'keterangan',
        'jumlah',
        'gambar',
       
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id'); // Menghubungkan ke tabel Barang
    }

    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class);
    }
}
