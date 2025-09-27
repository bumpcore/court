<?php

namespace BumpCore\Court\Tests\Unit;

use BumpCore\Court\GuardRunner;
use BumpCore\Court\Contracts\ObjectionCollector;
use BumpCore\Court\Tests\TestCase;

class GuardRunnerTest extends TestCase
{
    public function test_run_resolves_guard_and_calls_it_with_subject_and_collector_closure()
    {
        $resolver = new TestGuardResolver();
        $runner = new GuardRunner($resolver);

        $collector = new TestObjectionCollector();
        $closure = $collector->closure();

        $runner->run('test guard', 'test subject', $collector);

        $this->assertEquals(['test guard'], $resolver->resolvedWith);
        $this->assertEquals(['test subject', $closure], $resolver->lastGuard->calledWith);
    }
}

class TestGuardResolver
{
    public array $resolvedWith = [];
    public ?TestGuard $lastGuard = null;

    public function __invoke($guard)
    {
        $this->resolvedWith[] = $guard;
        $this->lastGuard = new TestGuard();
        return $this->lastGuard;
    }
}

class TestGuard
{
    public array $calledWith = [];

    public function __invoke($subject, $closure)
    {
        $this->calledWith = [$subject, $closure];
    }
}

class TestObjectionCollector implements ObjectionCollector
{
    private $closure;

    public function __construct()
    {
        $this->closure = function () {
            return 'mock objection';
        };
    }

    public function closure()
    {
        return $this->closure;
    }

    public function all()
    {
        return [];
    }
}
