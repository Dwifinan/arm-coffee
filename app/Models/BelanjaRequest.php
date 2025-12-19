<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaRequest extends Model
{
    use HasFactory;

    protected $table = 'permintaan_belanja';

    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'bahan_id',
        'jumlah',
        'total_harga'
    ];

    // Karena kita sudah membuat kolom created_at dan updated_at di migrasi,
    // kita biarkan Model ini menggunakan timestamps default Laravel.

    /**
     * Definisi relasi Many-to-One: Request ini terkait dengan satu Bahan.
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id', 'id');
    }
}
