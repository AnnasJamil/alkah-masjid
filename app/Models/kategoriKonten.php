<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kategoriKonten extends Model
{
    //
    protected $table = 'kategori_kontens';
    protected $fillable = ['nama_kategori'];

    public function kontenWeb()
    {
        return $this->hasMany(KontenWeb::class, 'kategori_konten_id');
    }
}
