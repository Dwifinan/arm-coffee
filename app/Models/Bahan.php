<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';
    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'harga_persatuan',
        'jumlah_satuan',
        'stok_minimal',
        'stok'
    ];
    // Catatan: 'harga_satuan' adalah kolom yang tersimpan (storedAs) dan tidak perlu di fillable.

    /**
     * Relasi One-to-Many: Bahan memiliki banyak entri di Komposisi
     */
    public function komposisi()
    {
        return $this->hasMany(Komposisi::class, 'bahan_id');
    }

    public function belanjaDetails()
    {
        return $this->hasMany(BelanjaDetail::class, 'bahan_id');
    }
}
