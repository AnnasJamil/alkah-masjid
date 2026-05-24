<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalKas extends Model
{
    protected $fillable = [
        'pembayaran_alkah_id',
        'infaq_id',
        'jenis_kas',
        'tanggal',
        'keterangan',
        'nominal',
    ];


//relasi ke pembayaran alkah
public function pembayaranAlkah() {
    return $this->belongsTo(PembayaranAlkah::class);
}

//relasi ke infaq
public function infaq() {
    return $this->belongsTo(Infaq::class);
}

}
