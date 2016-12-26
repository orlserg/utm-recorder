<?php
namespace Orlserg\UtmRecorder;

use Illuminate\Support\ServiceProvider;

class UtmRecorderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__.'/config/utm-recorder.php') => config_path('utm-recorder.php'),
        ]);

        if (!class_exists(CreateUtmRecorderTables::class)) {
            $from = __DIR__ . '/database/migrations/migrations.php';
            $to = database_path('/migrations/' . date('Y_m_d_His') . '_create_utm_recorder_tables.php');
            $this->publishes([$from => $to], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/utm-recorder.php', 'utm-recorder');
    }
}
