<?php

namespace BumpCore\Court\Tests\Unit;

use BumpCore\Court\ObjectionCollector;
use BumpCore\Court\Contracts\Objection;
use BumpCore\Court\Tests\TestCase;

class ObjectionCollectorTest extends TestCase
{
    public function test_new_objection_creates_and_collects_objection()
    {
        $factory = new TestObjectionFactory();
        $collector = new ObjectionCollector($factory);

        $objection = $collector->newObjection('test value');

        $this->assertInstanceOf(Objection::class, $objection);
        $this->assertEquals(['test value'], $factory->calledWith);
        $this->assertCount(1, $collector->all());
        $this->assertSame($objection, $collector->all()[0]);
    }

    public function test_closure_creates_objection_when_called()
    {
        $factory = new TestObjectionFactory();
        $collector = new ObjectionCollector($factory);

        $closure = $collector->closure();
        $objection = $closure('another value');

        $this->assertInstanceOf(Objection::class, $objection);
        $this->assertEquals(['another value'], $factory->calledWith);
        $this->assertCount(1, $collector->all());
        $this->assertSame($objection, $collector->all()[0]);
    }

    public function test_all_returns_empty_array_initially()
    {
        $factory = new TestObjectionFactory();
        $collector = new ObjectionCollector($factory);

        $this->assertEquals([], $collector->all());
    }

    public function test_all_returns_collected_objections()
    {
        $factory = new TestObjectionFactory();
        $collector = new ObjectionCollector($factory);

        $objection1 = $collector->newObjection('value1');
        $objection2 = $collector->newObjection('value2');

        $all = $collector->all();
        $this->assertCount(2, $all);
        $this->assertSame($objection1, $all[0]);
        $this->assertSame($objection2, $all[1]);
    }
}

class TestObjectionFactory
{
    public array $calledWith = [];

    public function __invoke($value): Objection
    {
        $this->calledWith[] = $value;
        return new MockObjection();
    }
}

class MockObjection implements Objection
{
}
