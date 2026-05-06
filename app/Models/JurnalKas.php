<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalKas extends Model
{
    //
    public $timestamps = false;

    protected $fillable = [
        'kategori_kas_id',
        'pembayaran_alkah_id',
        'infaq_id',
        'jenis_kas',
        'tanggal',
        'keterangan',
        'nominal', 
    ];


//relasi ke kategori kas
public function kategoriKas() {
    return $this->belongsTo(KategoriKas::class);
}

//relasi ke pembayaran alkah
public function pembayaranAlkah() {
    return $this->belongsTo(PembayaranAlkah::class);
}

//relasi ke infaq
public function infaq() {
    return $this->belongsTo(Infaq::class);
}

}
