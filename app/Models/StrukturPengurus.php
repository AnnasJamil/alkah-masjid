<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturPengurus extends Model
{
    //
    protected $table = 'struktur_penguruses';
    protected $fillable = [
        'nama',
        'jabatan',
        'foto_pengurus',
        'periode',
    ];
}
