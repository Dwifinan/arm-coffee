<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    use HasFactory;

    protected $table = 'komposisi';

    // Kolom yang dapat diisi
    protected $fillable = [
        'menu_id',
        'bahan_id',
        'jumlah_bahan',
        'satuan'
    ];

    /**
     * Relasi Many-to-One: Komposisi merujuk pada satu Bahan
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    /**
     * Relasi Many-to-One: Komposisi merujuk pada satu Menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
