<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanMasuk extends Model
{
    use HasFactory;

    // Nama tabel sesuai schema database
    protected $table = 'bahan_masuk';

    // Kolom yang dapat diisi (fillable) untuk operasi create/update
    // Pastikan semua kolom yang diisi di BelanjaController@selesai() ada di sini
    protected $fillable = [
        'bahan_id',
        'jumlah',
        'sisa_stok', // Added for FIFO
        'harga',
        'harga_satuan',
        'total_harga',
        'expired',
        // kolom created_at dan updated_at diaktifkan secara default oleh Laravel
    ];

    // Kolom yang harus diubah ke objek Carbon (tanggal/waktu)
    protected $casts = [
        'expired' => 'datetime',
    ];

    /**
     * Definisi relasi Many-to-One: BahanMasuk dimiliki oleh satu Bahan
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id', 'id');
    }
}