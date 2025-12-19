<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Belanja extends Model
{
    use HasFactory;

    protected $table = 'belanja';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(BelanjaDetail::class, 'belanja_id');
    }

    public function userRequest()
    {
        return $this->belongsTo(User::class, 'user_id_request');
    }

    public function userSelesai()
    {
        return $this->belongsTo(User::class, 'user_id_selesai');
    }
}
