<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarNasional extends Model
{
    protected $table = 'standar_nasional';
    protected $fillable = ['standar', 'keterangan', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(StandarNasional::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StandarNasional::class, 'parent_id');
    }

    public function allDescendants()
    {
        return $this->hasMany(StandarNasional::class, 'parent_id')->with('children');
    }

    public function level()
    {
        if (!$this->parent_id) {
            return 1; // Level 1: No parent
        }
        if ($this->parent && !$this->parent->parent_id) {
            return 2; // Level 2: Has parent but parent's parent is null
        }
        return 3; // Level 3: Has parent with a parent
    }
}