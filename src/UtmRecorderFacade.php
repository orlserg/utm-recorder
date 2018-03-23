<?php

namespace Orlserg\UtmRecorder;

use Illuminate\Support\Facades\Facade;

class UtmRecorderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UtmRecorder::class;
    }
}
