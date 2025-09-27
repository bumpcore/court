<?php

namespace BumpCore\Court;

/**
 * @template TObjection of Contracts\Objection
 */
class GuardRunner
{
    /**
     * The guard resolver callback.
     *
     * @var callable
     */
    protected $guardResolver;

    /**
     * Create a new guard runner instance.
     *
     * @param callable $guardResolver
     */
    public function __construct(callable $guardResolver)
    {
        $this->guardResolver = $guardResolver;
    }

    /**
     * Handle the given guard with the given subject and objection collector.
     *
     * @param mixed $guard
     * @param mixed $subject
     * @param Contracts\ObjectionCollector<TObjection> $collector
     *
     * @return void
     */
    public function run($guard, $subject, $collector)
    {
        $guard = $this->resolveGuard($guard);

        $guard($subject, $collector->closure());
    }

    /**
     * Resolve the given guard to a callable.
     *
     * @param mixed $guard
     *
     * @return (callable(mixed,mixed):void)
     */
    protected function resolveGuard($guard)
    {
        return ($this->guardResolver)($guard);
    }
}
