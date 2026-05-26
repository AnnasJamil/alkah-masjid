<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Alkah;
use App\Models\PembayaranAlkah;

class TransaksiAlkah extends Model
{
    //
    protected $fillable = [
        'user_id',
        'alkah_id',
        'kode_transaksi',
        'tanggal_pemesanan',
        'total',
        'status',

    ];

//relasi ke user
public function user() {
    return $this->belongsTo(User::class);
}

//relasi ke alkah
public function alkah() {
    return $this->belongsTo(Alkah::class);
}

//relasi ke pembayaran alkah
public function pembayaranAlkah() {
    return $this->hasOne(PembayaranAlkah::class);
}

}
