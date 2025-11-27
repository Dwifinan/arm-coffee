<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan schema SQL Anda
    protected $table = 'komposisi';

    // Kolom yang dapat diisi (fillable) untuk operasi create/update
    protected $fillable = [
        'menu_id',
        'bahan_id',
        'jumlah_bahan',
        'satuan'
    ];

    /**
     * Definisi relasi Many-to-One: Detail Menu (bahan) ini dimiliki oleh satu Menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    /**
     * Definisi relasi Many-to-One: Detail Menu ini menggunakan satu Bahan
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id', 'id');
    }
}
