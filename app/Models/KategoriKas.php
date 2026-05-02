<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKas extends Model
{
    //
    protected $fillable = [
        'nama_kategori',
    ];


//relasi ke jurnal kas
public function jurnalKas() {
    return $this->hasMany(JurnalKas::class);
}

}
