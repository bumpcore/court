<?php

namespace BumpCore\Court\Tests\Integration;

use BumpCore\Court\Factory;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class LaravelIntegrationTest extends TestCase
{
    public function test_default_guard_resolver_is_overridden(): void
    {
        $this->assertNotNull(
            Factory::getGuardResolver()
        );
    }

    public function test_guard_resolver_resolves_classes_via_laravel_container(): void
    {
        $this->app->bind(DependencyA::class, function () {
            return new DependencyB();
        });

        $resolver = Factory::getGuardResolver();

        $resolver(SomeClass::class);

        $this->assertSame(
            DependencyB::class,
            SomeClass::$resolved
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            \BumpCore\Court\CourtServiceProvider::class,
        ];
    }
}

class DependencyA
{
    // ...
}

class DependencyB extends DependencyA
{
    // ...
}

class SomeClass
{
    public static $resolved;

    public function __construct(public DependencyA $dependency)
    {
        self::$resolved = get_class($dependency);
    }

    public function handle($subject, $objection)
    {
        // Handle the subject and objection
    }
}
