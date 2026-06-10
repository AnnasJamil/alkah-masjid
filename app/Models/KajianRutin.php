<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KajianRutin extends Model
{
    //
    protected $fillable = [
        'pemateri',
        'judul',
        'jadwal',
        'jam',
        'lokasi',
        'gambar',
    ];
}
