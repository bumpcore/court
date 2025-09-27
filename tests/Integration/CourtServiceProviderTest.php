<?php

namespace BumpCore\Court\Tests\Integration;

use BumpCore\Court\Factory;
use Orchestra\Testbench\PHPUnit\TestCase;

class CourtServiceProviderTest extends TestCase
{
    public function test_boot_sets_guard_resolver()
    {
        $this->assertNotNull(
            Factory::getGuardResolver()
        );
    }
}
