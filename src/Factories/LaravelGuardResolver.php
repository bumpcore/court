<?php

namespace BumpCore\Court\Factories;

use Illuminate\Contracts\Container\Container;

class LaravelGuardResolver extends GuardResolver
{
    /**
     * The Laravel application container.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Create a new LaravelGuardResolver instance.
     *
     * @param Container $app The Laravel application container.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create an instance of a class using the Laravel container.
     *
     * @template TClass
     *
     * @param class-string<TClass> $class
     *
     * @return TClass
     */
    protected function createInstance($class)
    {
        return $this->app->make($class);
    }
}
