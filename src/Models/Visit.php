<?php

namespace Orlserg\UtmRecorder\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsToMany(config('utm-recorder.link_visits_with_model'), 'user_id');
    }
}
