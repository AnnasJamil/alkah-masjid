<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alkah;

class BlokAlkah extends Model
{
    //
    protected $fillable = [
        'kode_blok',
        'status',
    ];


//relasi ke alkah
public function alkah() {
    return $this->hasMany(Alkah::class);
}

}


