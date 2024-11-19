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
        'status',
       
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id'); // Menghubungkan ke tabel Barang
    }

    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class);
    }

    // protected static function booted()
    // {
    //     static::created(function ($peminjaman) {
    //         // Kurangi stok barang
    //         $barang = $peminjaman->barang;
    //         if ($barang) {
    //             $barang->decrement('qty', $peminjaman->jumlah);
    //         }
    //     });

    //     static::deleting(function ($peminjaman) {
    //         // Kembalikan stok barang ketika peminjaman dihapus
    //         $barang = $peminjaman->barang;
    //         if ($barang) {
    //             $barang->increment('qty', $peminjaman->jumlah);
    //         }
    //     });
    // }
    
}
