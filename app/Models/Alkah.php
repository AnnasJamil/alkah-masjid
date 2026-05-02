<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BlokAlkah;

class Alkah extends Model
{
    //
    protected $fillable = [
        'blok_alkah_id',
        'kode_alkah',
        'harga',
        'status',
    ];


public function blokAlkah() {
    return $this->belongsTo(BlokAlkah::class);
}

}
