<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetInfaq extends Model
{
    //
    protected $fillable = [
        'nama_target',
        'target_dana',
    ];

    public function infaqs()
    {
        return $this->hasMany(Infaq::class, 'target_infaq_id');
    }
}
