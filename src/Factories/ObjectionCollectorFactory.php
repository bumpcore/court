<?php

namespace BumpCore\Court\Factories;

use BumpCore\Court\Contracts\Objection;
use BumpCore\Court\ObjectionCollector;

class ObjectionCollectorFactory
{
    /**
     * @var callable
     */
    protected $objectionFactory;

    /**
     * @param callable(mixed): Objection $objectionFactory
     */
    public function __construct(callable $objectionFactory)
    {
        $this->objectionFactory = $objectionFactory;
    }

    /**
     * Create a new objection collector.
     *
     * @return \BumpCore\Court\Contracts\ObjectionCollector<Objection>
     */
    public function __invoke()
    {
        return new ObjectionCollector($this->objectionFactory);
    }
}
