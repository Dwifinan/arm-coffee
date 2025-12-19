<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaDetail extends Model
{
    use HasFactory;

    protected $table = 'belanja_detail';
    protected $guarded = [];

    public function belanja()
    {
        return $this->belongsTo(Belanja::class, 'belanja_id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
