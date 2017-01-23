<?php

namespace Orlserg\UtmRecorder;

use Illuminate\Support\Facades\Facade;

class UtmRecorderFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return UtmRecorder::class;
    }
}
