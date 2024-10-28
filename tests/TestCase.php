<?php

namespace Goedemiddag\RequestResponseLog\Tests;

use Goedemiddag\RequestResponseLog\RequestResponseLogServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * @param Application $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            RequestResponseLogServiceProvider::class,
        ];
    }
}
