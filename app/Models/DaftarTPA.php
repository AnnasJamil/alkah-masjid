<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarTPA extends Model
{
    //
    protected $table = 'daftar_tpas';
    protected $fillable = [
        'nama_anak',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_orangtua',
        'no_hp',
        'alamat',
        'tanggal_daftar',
    ];
}
