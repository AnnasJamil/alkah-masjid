<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\JurnalKas;

class Infaq extends Model
{
    //
    protected $fillable = [
        'nominal',
        'bukti_infaq',
        'status',
        'tanggal_infaq',
    ];

    //relasi ke jurnal kas
    public function jurnalKas() {
        return $this->hasOne(JurnalKas::class);
    }
}
