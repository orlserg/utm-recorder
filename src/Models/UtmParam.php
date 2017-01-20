<?php

namespace Orlserg\UtmRecorder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orlserg\UtmRecorder\Exceptions\UtmParamNotFound;

class UtmParam extends Model
{
    protected $guarded = ['id'];

    public function visits()
    {
        return $this->belongsToMany(Visit::class);
    }

    public function getId($name)
    {
        $cache = config('utm-recorder.cache_utm');

        if ($cache) {
            if (cache()->has($name)) {
                return cache($name);
            }
        }

        $id = static::whereName($name)->first()->id;
        if ($cache) {
            cache([$name, $id], config('utm-recorder.cache_time'));
        }

        return $id;
    }
}
