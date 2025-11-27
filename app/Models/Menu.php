<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan schema SQL Anda
    protected $table = 'menu';

    // Kolom yang dapat diisi (fillable) untuk operasi create/update
    protected $fillable = [
        'nama',
        'harga'
    ];

    /**
     * Definisi relasi One-to-Many: Menu memiliki banyak Detail Bahan (MenuDetail)
     */
    public function details()
    {
        return $this->hasMany(MenuDetail::class, 'menu_id', 'id');
    }
}
