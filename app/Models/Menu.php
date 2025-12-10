<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan schema SQL Anda
    protected $table = 'menu';

    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'nama',
        'harga',
        'tersedia' // Tambahkan kolom 'tersedia' dari skema migrasi Anda
    ];

    /**
     * Definisi relasi One-to-Many: Menu memiliki banyak Komposisi (Resep)
     */
    public function komposisi()
    {
        // Mengubah nama fungsi dari details() menjadi komposisi() agar lebih jelas
        return $this->hasMany(Komposisi::class, 'menu_id', 'id');
    }

    /**
     * Relasi One-to-Many: Menu memiliki banyak Penjualan
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'menu_id');
    }
}
