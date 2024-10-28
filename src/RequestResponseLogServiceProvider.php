<?php

namespace Goedemiddag\RequestResponseLog;

use Illuminate\Support\ServiceProvider;

class RequestResponseLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            paths: [
                __DIR__ . '/../config/request-response-log.php' => config_path('request-response-log.php'),
            ],
            groups: 'config',
        );

        $this->mergeConfigFrom(
            path: __DIR__ . '/../config/request-response-log.php',
            key: 'request-response-log',
        );

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
