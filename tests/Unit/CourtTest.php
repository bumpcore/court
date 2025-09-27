<?php

namespace BumpCore\Court\Tests\Unit;

use BumpCore\Court\Court;
use BumpCore\Court\Factory;
use BumpCore\Court\Objection;
use BumpCore\Court\Tests\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class CourtTest extends TestCase
{
    public function test_court_can_be_created_with_subject()
    {
        $subject = 'I love elephants';

        $court = new Court($subject);

        $this->assertInstanceOf(Court::class, $court);

        $court = Court::of($subject);

        $this->assertInstanceOf(Court::class, $court);
    }

    public function test_court_returns_empty_objection_bag_with_no_guards()
    {
        $result = Court::of('I love elephants')->verdict();

        $this->assertTrue($result->isEmpty());
    }

    public function test_court_collects_objections_from_guards()
    {
        $results = Court::of('I love elephants')->guards([
            function ($subject, $objection) {
                if (str_contains($subject, 'elephants')) {
                    $objection('No elephants allowed!');
                }
            },
            function ($subject, $objection) {
                if (str_contains($subject, 'bears')) {
                    $objection('No bears allowed!');
                }
            },
        ])
            ->verdict();

        $this->assertFalse($results->isEmpty());
        $this->assertCount(1, $results->all());

        /**
         * @var array<Objection>
         */
        $objections = $results->all();
        $this->assertContainsOnlyInstancesOf(Objection::class, $objections);
        $this->assertEquals('No elephants allowed!', $objections[0]->value());
    }

    public function test_court_accepts_variadic_guards()
    {
        $results = Court::of('I love elephants')->guards(
            function ($subject, $objection) {
                if (str_contains($subject, 'elephants')) {
                    $objection('No elephants allowed!');
                }
            },
            function ($subject, $objection) {
                if (str_contains($subject, 'bears')) {
                    $objection('No bears allowed!');
                }
            },
        )
            ->verdict();

        $this->assertFalse($results->isEmpty());
        $this->assertCount(1, $results->all());

        /**
         * @var array<Objection>
         */
        $objections = $results->all();
        $this->assertContainsOnlyInstancesOf(Objection::class, $objections);
        $this->assertEquals('No elephants allowed!', $objections[0]->value());
    }

    public function test_court_can_have_objection_factory()
    {
        $objections = Court::of('I love elephants')
            ->setObjectionFactory(fn ($value) => new class ($value) extends Objection {
                public function value()
                {
                    return 'Custom: ' . $this->value;
                }
            })
            ->guards(fn ($subject, $objection) => $objection('An objection'))
            ->verdict();

        $this->assertFalse($objections->isEmpty());
        $this->assertEquals('Custom: An objection', $objections->all()[0]->value());
    }

    public function test_court_can_have_objection_bag_factory()
    {
        $objections = Court::of('I love elephants')
            ->setObjectionBagFactory(fn ($objections) => new class ($objections) extends \BumpCore\Court\ObjectionBag {
                public function all(): array
                {
                    return array_map(fn ($objection) => 'Custom: ' . $objection->value(), parent::all());
                }
            })
            ->guards(fn ($subject, $objection) => $objection('An objection'))
            ->verdict();

        $this->assertFalse($objections->isEmpty());
        $this->assertEquals('Custom: An objection', $objections->all()[0]);
    }

    public function test_court_can_have_guard_resolver()
    {
        $result = Court::of('I love elephants')
            ->setGuardResolver(fn ($guard) => $guard === 'Neat.'
                ? fn ($s, $o) => $o('Objection from Neat.')
                : fn ($s, $o) => $guard($s, $o))
                ->guards(
                    'Neat.',
                    fn ($subject, $objection) => $objection('Objection from closure.'),
                )
            ->verdict();

        $this->assertFalse($result->isEmpty());
        $this->assertCount(2, $result->all());
        $this->assertEquals('Objection from Neat.', $result->all()[0]->value());
        $this->assertEquals('Objection from closure.', $result->all()[1]->value());
    }

    public function test_court_can_have_objection_collector_factory()
    {
        $results = Court::of('I love elephants')
            ->setObjectionCollectorFactory(fn () => new class () implements \BumpCore\Court\Contracts\ObjectionCollector {
                /**
                 * @inheritDoc
                 */
                public function all()
                {
                    return [
                        'Custom objection from collector.'
                    ];
                }

                /**
                 * @inheritDoc
                 */
                public function closure()
                {
                    return fn () => null;
                }
            })
            ->guards(fn ($subject, $objection) => $objection('An objection'))
            ->verdict();

        $this->assertCount(1, $results->all());
        $this->assertEquals('Custom objection from collector.', $results->all()[0]);
    }

    #[RunInSeparateProcess]
    public function test_court_factories_are_high_priority()
    {
        Factory::setGuardResolver(
            fn () => throw new \Exception('This should not be called.')
        );

        Factory::setObjectionCollectorFactory(
            fn () => throw new \Exception('This should not be called.')
        );

        Factory::setObjectionBagFactory(
            fn () => throw new \Exception('This should not be called.')
        );

        Factory::setObjectionFactory(
            fn () => throw new \Exception('This should not be called.')
        );

        $results = Court::of('I love elephants')
            ->setObjectionCollectorFactory(fn () => new class () implements \BumpCore\Court\Contracts\ObjectionCollector {
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
            })
            ->setGuardResolver(
                fn ($guard) => $guard === 'Neat.'
                    ? fn ($s, $o) => $o('Objection from Neat.')
                    : fn ($s, $o) => $guard($s, $o)
            )
            ->setObjectionBagFactory(
                fn ($objections) => new class ($objections) extends \BumpCore\Court\ObjectionBag {
                    public function all(): array
                    {
                        return array_map(fn ($objection) => 'Custom: ' . $objection, parent::all());
                    }
                }
            )
            ->guards(
                'Neat.',
                fn ($subject, $objection) => $objection('An objection')
            )
            ->verdict();

        $this->assertCount(2, $results->all());
        $this->assertEquals('Custom: Collector: Objection from Neat.', $results->all()[0]);
        $this->assertEquals('Custom: Collector: An objection', $results->all()[1]);
    }
}
