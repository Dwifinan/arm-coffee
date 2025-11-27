<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';

    // Properti yang Anda berikan
    protected $guarded = ["id"];
    public $timestamps = false;

    // --- Relasi (Penting untuk MenuController) ---
    /**
     * Definisi relasi One-to-Many: Bahan dapat digunakan di banyak Detail Menu
     */
    public function menuDetails()
    {
        // Asumsi MenuDetail Model sudah ada
        return $this->hasMany(MenuDetail::class, 'bahan_id', 'id');
    }
}
