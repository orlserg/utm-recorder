<?php

namespace Orlserg\UtmRecorder\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'params'
    ];

    protected $utms = [];

//    public function owner()
//    {
//        return $this->belongsToMany(config('utm-recorder.link_visits_with_model'), 'user_id');
//    }

    public function params()
    {
        return $this->belongsToMany(UtmParam::class, 'utm_contents')
            ->withPivot('content');
    }

    public function setUtms($utms)
    {
        $this->utms = $utms;
    }

    public function getUtms()
    {
        return $this->utms;
    }
}
