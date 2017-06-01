<?php

namespace Orlserg\UtmRecorder\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'params'
    ];

    protected $utms = [];

    public function owner()
    {
        return $this->belongsToMany(config('utm-recorder.link_visits_with_model'), 'owner_id');
    }

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
        return $this->prepareUtms($this->utms);
    }

    public function getUtmsAsArray()
    {
        $result = [];
        foreach ($this->params as $param) {
            $data = $param->getOriginal();
            $result[$data['name']] = $data['pivot_content'];
        }
        return $result;
    }

    /**
     * Create data array for sync()
     *
     * @param $utms
     * @return array
     */
    protected function prepareUtms($utms)
    {
        $param = new UtmParam();
        $result = [];

        foreach ($utms as $key => $utm) {
            $result[$param->getId($key)] = ['content' => $utm];
        }

        return $result;
    }

    public function scopeLastSourced($query, Carbon $date = null)
    {
        $date = $date ?: Carbon::now()->subMonth(1);

        return $query->where('created_at', '>=', $date->toDateTimeString())
            ->whereHas('params', function ($q) {
                $q->where('utm_param_id', '=', 1);
            })
            ->latest()
            ->take(1);
    }
}
