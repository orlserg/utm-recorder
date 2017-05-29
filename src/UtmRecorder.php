<?php

namespace Orlserg\UtmRecorder;

use Orlserg\UtmRecorder\Models\UtmParam;

class UtmRecorder
{
    /**
     * Save visits to db linked with concrete owner
     *
     * @param $owner
     */
    public function saveRecords($owner)
    {
        $key = config('utm-recorder.session_key');

        $visits = session($key) ?: [];
        if ($visits) {
            $owner->visits()->saveMany($visits);

            foreach ($visits as $visit) {
                $data = $visit->getUtms();
                $visit->params()->sync($data);
            }
        }

        session()->forget([$key]);
    }


}
