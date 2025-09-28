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
     * @return callable(mixed, ObjectionCollector<Objection>): void
     */
    public function __invoke($guard)
    {
        if (is_string($guard) && class_exists($guard)) {
            $guard = $this->createInstance($guard);
        }

        if (is_object($guard)) {
            return $this->fromObject($guard);
        }

        // - Callable (closure, function name, etc.)
        if (is_callable($guard)) {
            return fn ($subject, $objection) => $guard($subject, $objection);
        }

        throw new \InvalidArgumentException('Guard must be a callable, or a class name of an invokable class, or a class with a "verdict" or "handle" method.');
    }

    /**
     * Create a callable from an object.
     *
     * @param object $guard The guard object.
     *
     * @return callable(mixed, ObjectionCollector<Objection>): void
     */
    protected function fromObject($guard)
    {
        // - Invokable class
        if (is_callable($guard)) {
            return fn ($subject, $objection) => $guard($subject, $objection);
        }

        // - Class with a "verdict" method
        if (method_exists($guard, 'verdict')) {
            return fn ($subject, $objection) => $guard->verdict($subject, $objection);
        }

        // - Class with a "handle" method
        if (method_exists($guard, 'handle')) {
            return fn ($subject, $objection) => $guard->handle($subject, $objection);
        }

        throw new \InvalidArgumentException('Guard must be a callable, or a class name of an invokable class, or a class with a "verdict" or "handle" method.');
    }

    /**
     * Create an instance of a class.
     *
     * @template TClass
     *
     * @param class-string<TClass> $class
     *
     * @return TClass
     */
    protected function createInstance($class)
    {
        return new $class();
    }
}
