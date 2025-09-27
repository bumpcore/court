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
        Factory::setGuardResolver($this->resolve(...));
    }

    /**
     * Resolve a guard definition into a callable.
     *
     * @param mixed $guard
     *
     * @throws \InvalidArgumentException
     *
     * @return callable(mixed, ObjectionCollector<Objection>): void
     */
    protected function resolve($guard)
    {
        if (is_string($guard) && class_exists($guard)) {
            $instance = $this->app->make($guard);

            // - Invokable class
            if (is_callable($instance)) {
                return fn ($subject, $objection) => $instance($subject, $objection);
            }

            // - Class with a "verdict" method
            if (is_object($instance) && method_exists($instance, 'verdict')) {
                return fn ($subject, $objection) => $instance->verdict($subject, $objection);
            }

            // - Class with a "handle" method
            if (is_object($instance) && method_exists($instance, 'handle')) {
                return fn ($subject, $objection) => $instance->handle($subject, $objection);
            }
        }

        if (is_callable($guard)) {
            return fn ($subject, $objection) => $guard($subject, $objection);
        }

        throw new \InvalidArgumentException('Guard must be a callable, or a class name of an invokable class, or a class with a "verdict" or "handle" method.');
    }
}
