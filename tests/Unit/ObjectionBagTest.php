<?php

namespace BumpCore\Court\Tests\Unit;

use BumpCore\Court\ObjectionBag;
use BumpCore\Court\Tests\TestCase;

class ObjectionBagTest extends TestCase
{
    public function test_objection_bag_returns_all_objections()
    {
        $bag = new ObjectionBag([
            'first objection',
            'second objection',
        ]);

        $this->assertCount(2, $bag->all());
        $this->assertEquals('first objection', $bag->all()[0]);
        $this->assertEquals('second objection', $bag->all()[1]);
    }

    public function test_objection_bag_can_check_empty_or_not()
    {
        $bag = new ObjectionBag();

        $this->assertTrue($bag->isEmpty());
        $this->assertFalse($bag->isNotEmpty());

        $bag = new ObjectionBag(['an objection']);

        $this->assertFalse($bag->isEmpty());
        $this->assertTrue($bag->isNotEmpty());
    }

    public function test_objection_bags_can_be_merged()
    {
        $bag1 = new ObjectionBag(['first objection']);
        $bag2 = new ObjectionBag(['second objection']);

        $mergedBag = $bag1->merge($bag2);

        $this->assertCount(2, $mergedBag->all());
        $this->assertEquals('first objection', $mergedBag->all()[0]);
        $this->assertEquals('second objection', $mergedBag->all()[1]);
    }
}
