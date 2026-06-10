<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\KategoriKonten;
use App\Models\User;

class KontenWeb extends Model
{
    //
    protected $table = 'konten_webs';
    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'isi',
        'gambar',
        'tanggal_publish',
        'status'
        ];


    //relasi konten web dengan kategori konten
    public function kategoriKonten()
    {
        return $this->belongsTo(KategoriKonten::class, 'kategori_konten_id');
    }

    //relasi konten web dengan user    public function user()
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
