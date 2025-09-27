<?php

namespace BumpCore\Court\Tests\Unit\Factory;

use BumpCore\Court\Factories\GuardResolver;
use BumpCore\Court\Tests\TestCase;

class GuardResolverTest extends TestCase
{
    public function test_resolve_callable()
    {
        $resolver = new GuardResolver();
        $called = false;
        $guard = function ($subject, $objection) use (&$called) {
            $called = true;
        };

        $resolved = $resolver($guard);
        $resolved('subject', null);

        $this->assertTrue($called);
    }

    public function test_resolve_invokable_class()
    {
        $resolver = new GuardResolver();
        $guard = TestInvokableGuard::class;

        $resolved = $resolver($guard);
        $resolved('subject', null);

        $this->assertTrue(TestInvokableGuard::$called);
    }

    public function test_resolve_verdict_class()
    {
        $resolver = new GuardResolver();
        $guard = TestVerdictGuard::class;

        $resolved = $resolver($guard);
        $resolved('subject', null);

        $this->assertTrue(TestVerdictGuard::$called);
    }

    public function test_resolve_handle_class()
    {
        $resolver = new GuardResolver();
        $guard = TestHandleGuard::class;

        $resolved = $resolver($guard);
        $resolved('subject', null);

        $this->assertTrue(TestHandleGuard::$called);
    }

    public function test_invalid_guard_throws_exception()
    {
        $resolver = new GuardResolver();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Guard must be a callable, or a class name of an invokable class, or a class with a "verdict" or "handle" method.');

        $resolver(123);
    }
}

class TestInvokableGuard
{
    public static $called = false;

    public function __invoke($subject, $objection)
    {
        self::$called = true;
    }
}

class TestVerdictGuard
{
    public static $called = false;

    public function verdict($subject, $objection)
    {
        self::$called = true;
    }
}

class TestHandleGuard
{
    public static $called = false;

    public function handle($subject, $objection)
    {
        self::$called = true;
    }
}
