<?php

namespace Orlserg\UtmRecorder\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'params'
    ];

    public function user()
    {
        return $this->belongsToMany(config('utm-recorder.link_visits_with_model'), 'user_id');
    }

    public function params()
    {
        return $this->belongsToMany(UtmParam::class)->withPivot('content');
    }
}
