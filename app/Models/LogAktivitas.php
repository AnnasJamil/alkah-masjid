<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    //
    public $table = 'log_aktivitas';
    protected $fillable = [
        'user_id',
        'aktivitas',
        'waktu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
