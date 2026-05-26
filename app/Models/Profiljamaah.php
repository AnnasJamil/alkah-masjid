<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiljamaah extends Model
{
    //
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'foto_ktp',
        'foto_kk',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
