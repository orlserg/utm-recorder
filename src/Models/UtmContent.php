<?php

namespace Orlserg\UtmRecorder\Models;

use Illuminate\Database\Eloquent\Model;

class UtmContent extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function visit()
    {
        return $this->belongsToMany(Visit::class);
    }
}
