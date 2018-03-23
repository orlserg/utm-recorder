<?php

namespace Orlserg\UtmRecorder;

use Illuminate\Support\ServiceProvider;

class UtmRecorderServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
    }

    public function register()
    {
        $this->publishes([__DIR__ . '/../config/utm-recorder.php' => 'utm-recorder.php']);

        $this->app->singleton(UtmRecorder::class, function () {
            return new UtmRecorder();
        });
    }

    public function provides()
    {
        return [
            UtmRecorder::class
        ];
    }
}
