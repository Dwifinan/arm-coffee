<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanKeluar extends Model
{
    use HasFactory;

    protected $table = 'bahan_keluar';

    /**
     * Kolom-kolom yang dapat diisi (fillable) untuk mencegah Mass Assignment Exception.
     * Kolom harus sesuai dengan data yang dikirim oleh PenjualanController.
     */
    protected $fillable = [
        'bahan_id',
        'jumlah',
        'harga',
        'harga_satuan',
        'total_harga',
        'expired'
    ];

    /**
     * Definisikan relasi Many-to-One: Bahan Keluar merujuk pada satu Bahan.
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
