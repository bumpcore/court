<?php

namespace BumpCore\Court\Tests\Unit\Factory;

use BumpCore\Court\Court;
use BumpCore\Court\Factory;
use BumpCore\Court\Tests\TestCase;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

#[RunClassInSeparateProcess()]
class FactoryTest extends TestCase
{
    public function test_can_set_global_objection_factory()
    {
        Factory::setObjectionFactory(fn ($value) => 'Custom Objection: ' . $value);

        $results = Court::of('I love elephants')
            ->guards(fn ($s, $o) => $o('No elephants allowed!'))
            ->verdict();

        $this->assertSame('Custom Objection: No elephants allowed!', $results->all()[0]);
    }

    public function test_can_set_global_guard_resolver()
    {
        Factory::setGuardResolver(fn ($guard) => fn ($s, $o) => $o('Custom objection from collector.'));

        $results = Court::of('I love elephants')
            ->guards(fn ($s, $o) => $o('No elephants allowed!'))
            ->verdict();

        $this->assertSame('Custom objection from collector.', $results->all()[0]->value());
    }

    public function test_can_set_global_objection_collector_factory()
    {
        Factory::setObjectionCollectorFactory(
            fn () => new class () implements \BumpCore\Court\Contracts\ObjectionCollector {
                protected $collected = [];

                /**
                 * @inheritDoc
                 */
                public function all()
                {
                    return $this->collected;
                }

                /**
                 * @inheritDoc
                 */
                public function closure()
                {
                    return function ($value) {
                        $this->collected[] = 'Collector: ' . $value;
                    };
                }
            }
        );

        $results = Court::of('I love elephants')
            ->guards(fn ($subject, $objection) => $objection('An objection'))
            ->verdict();

        $this->assertSame('Collector: An objection', $results->all()[0]);
    }

    public function test_can_set_global_objection_bag_factory()
    {
        Factory::setObjectionBagFactory(fn ($objections) => new class ($objections) extends \BumpCore\Court\ObjectionBag {
            public function all(): array
            {
                return array_map(fn ($objection) => 'Custom: ' . $objection->value(), parent::all());
            }
        });

        $objections = Court::of('I love elephants')
            ->guards(fn ($subject, $objection) => $objection('An objection'))
            ->verdict();

        $this->assertFalse($objections->isEmpty());
        $this->assertEquals('Custom: An objection', $objections->all()[0]);
    }
}
