<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubStandarMutu extends Model
{
    protected $table = 'sub_standar_mutu';
    protected $fillable = [
        'standar_mutu_id',
        'prodi_id',
        'sub_standar',
        'indikator_0',
        'indikator_1',
        'indikator_2',
        'indikator_3',
        'indikator_4',
        'target',
    ];

    public function standarMutu()
    {
        return $this->belongsTo(StandarMutu::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}