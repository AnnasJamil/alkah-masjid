<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAlmarhum extends Model
{
    //
    protected $fillable = [
        'alkah_id',
        'nama_almarhum',
        'tanggal_lahir',
        'tanggal_wafat',
        'umur',
    ];

    public function alkah()
    {
        return $this->belongsTo(Alkah::class);
    }
}
