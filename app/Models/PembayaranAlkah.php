<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TransaksiAlkah;
use App\Models\JurnalKas;

class PembayaranAlkah extends Model
{
    //
    protected $fillable = [
        'transaksi_alkah_id',
        'bukti_pembayaran',
        'total_bayar',
        'status',
        'catatan',
        'tanggal_bayar',
    ];



public function transaksiAlkah() {
    return $this->belongsTo(TransaksiAlkah::class);
}

public function jurnalKas() {
    return $this->hasOne(JurnalKas::class);
}

}
