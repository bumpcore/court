<?php

namespace BumpCore\Court;

use BumpCore\Court\Contracts\Objection;
use BumpCore\Court\Contracts\ObjectionCollector;
use Illuminate\Support\ServiceProvider;

class CourtServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap court services.
     *
     * @return void
     */
    public function boot()
    {
        Factory::setGuardResolver(function ($guard) {
            return (new Factories\LaravelGuardResolver($this->app))($guard);
        });
    }
}
