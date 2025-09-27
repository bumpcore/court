<?php

namespace BumpCore\Court\Tests\Unit;

use BumpCore\Court\Objection;
use BumpCore\Court\Tests\TestCase;

class ObjectionTest extends TestCase
{
    public function test_constructor_sets_value()
    {
        $value = 'test value';
        $objection = new Objection($value);

        $this->assertEquals($value, $objection->value());
    }

    public function test_value_returns_stored_value()
    {
        $objection = new Objection(42);

        $this->assertEquals(42, $objection->value());
    }

    public function test_value_works_with_different_types()
    {
        $stringValue = 'string';
        $intValue = 123;
        $arrayValue = ['key' => 'value'];
        $nullValue = null;

        $objection1 = new Objection($stringValue);
        $objection2 = new Objection($intValue);
        $objection3 = new Objection($arrayValue);
        $objection4 = new Objection($nullValue);

        $this->assertEquals($stringValue, $objection1->value());
        $this->assertEquals($intValue, $objection2->value());
        $this->assertEquals($arrayValue, $objection3->value());
        $this->assertEquals($nullValue, $objection4->value());
    }
}
