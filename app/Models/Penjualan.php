<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = ['kode_transaksi', 'menu_id', 'jumlah', 'total_harga'];

    // Relasi: Setiap penjualan merujuk pada satu menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
