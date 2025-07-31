<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarMutu extends Model
{
    protected $table = 'standar_mutu';
    protected $fillable = [
        'tahun_id', 
        'lembaga_akreditasi_id', 
        'standar_nasional_id', 
        'status', 
        'nilai_mutu',
        'bukti_dokumen',
        'komentar_auditee',
        'komentar_auditor'
    ];

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function lembagaAkreditasi()
    {
        return $this->belongsTo(LembagaAkreditasi::class);
    }

    public function standarNasional()
    {
        return $this->belongsTo(StandarNasional::class);
    }

    public function subStandars()
    {
        return $this->hasMany(SubStandarMutu::class);
    }

    public function isBuktiDokumenFile()
    {
        return $this->bukti_dokumen && !filter_var($this->bukti_dokumen, FILTER_VALIDATE_URL);
    }

    public function isBuktiDokumenLink()
    {
        return $this->bukti_dokumen && filter_var($this->bukti_dokumen, FILTER_VALIDATE_URL);
    }
}