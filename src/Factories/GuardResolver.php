<?php

namespace BumpCore\Court\Factories;

use BumpCore\Court\Contracts\Objection;
use BumpCore\Court\Contracts\ObjectionCollector;

class GuardResolver
{
    /**
     * Resolve a guard into a callable function.
     *
     * @param mixed $guard The guard to resolve. It can be:
     *                     - A callable (closure, function name, etc.)
     *                     - A class name of an invokable class
     *                     - A class name of a class with a "verdict" method
     *                     - A class name of a class with a "handle" method
     *
     * @return callable(mixed, ObjectionCollector<Objection>): void A callable function that takes two parameters: $subject and $objection.
     */
    public function __invoke($guard)
    {
        if (is_string($guard) && class_exists($guard)) {
            $instance = new $guard();

            // - Invokable class
            if (is_callable($instance)) {
                return fn ($subject, $objection) => $instance($subject, $objection);
            }

            // - Class with a "verdict" method
            if (method_exists($instance, 'verdict')) {
                return fn ($subject, $objection) => $instance->verdict($subject, $objection);
            }

            // - Class with a "handle" method
            if (method_exists($instance, 'handle')) {
                return fn ($subject, $objection) => $instance->handle($subject, $objection);
            }
        }

        // - Callable (closure, function name, etc.)
        if (is_callable($guard)) {
            return fn ($subject, $objection) => $guard($subject, $objection);
        }

        throw new \InvalidArgumentException('Guard must be a callable, or a class name of an invokable class, or a class with a "verdict" or "handle" method.');
    }
}
