<?php

namespace BumpCore\Court\Tests\Integration;

use BumpCore\Court\Factory;
use BumpCore\Court\Tests\LaravelTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class CourtServiceProviderTest extends LaravelTestCase
{
    public function test_boot_sets_guard_resolver()
    {
        $this->assertNotNull(
            Factory::getGuardResolver()
        );
    }
}
