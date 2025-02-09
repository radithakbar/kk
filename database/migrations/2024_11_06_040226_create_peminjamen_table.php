<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table ->string('nama_siswa')->nullable();
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian');
            $table ->string('keterangan')->nullable();
            $table->integer('jumlah')->nullable();
            $table->enum('status', ['dikembalikan', 'belum_dikembalikan'])->default('belum_dikembalikan');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
