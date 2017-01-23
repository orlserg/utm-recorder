# UtmRecorder

UtmRecorder help you record utm parameters from visitors and visited urls.

# Documentation

## Installation

``` bash
$ composer require orlserg/utm-recorder
```

Add the service provider and (optionally) alias to their relative arrays in config/app.php:

``` php

    'providers' => [
        ...
        \Orlserg\UtmRecorder\UtmRecorderServiceProvider::class,
    ],

...

    'aliases' => [
        ...
        'UtmRecorder' => \Orlserg\UtmRecorder\UtmRecorderFacade::class,
    ],

```

Publish the config and migration files:

``` php
php artisan vendor:publish --provider="Orlserg\UtmRecorder\UtmRecorderServiceProvider"
```

Add the `\Orlserg\UtmRecorder\Middleware\UtmRecorder::class` middleware to `App\Http\Kernel.php` after the `EncryptCookie` middleware:

```php
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Kyranb\Footprints\Middleware\CaptureAttributionDataMiddleware::class,
    ];
```


Go over the configuration file, and set up settings:

```php
    // your own visitor table
    'link_visits_with' => 'visitors',

    // your own visitor model
    'link_visits_with_model' => \App\Visitor::class,
```

Run:

``` bash
$ php artisan migrate
```
Link your own visitor model with visits table like so:

```php
    public function visits()
    {
        return $this->HasMany(Visit::class, 'owner_id', 'id');
    }
```


