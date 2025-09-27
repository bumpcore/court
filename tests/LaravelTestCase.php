<?php

namespace BumpCore\Court\Tests;

use Orchestra\Testbench\TestCase;

class LaravelTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \BumpCore\Court\CourtServiceProvider::class,
        ];
    }
}
