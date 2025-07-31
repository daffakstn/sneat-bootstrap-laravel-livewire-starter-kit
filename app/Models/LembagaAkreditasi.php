<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LembagaAkreditasi extends Model
{
    protected $table = 'lembaga_akreditasi';
    protected $fillable = ['lembaga', 'keterangan'];
}