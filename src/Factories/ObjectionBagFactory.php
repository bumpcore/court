<?php

namespace BumpCore\Court\Factories;

use BumpCore\Court\Contracts\Objection;
use BumpCore\Court\ObjectionBag;

class ObjectionBagFactory
{
    /**
     * Invoke the factory to create an ObjectionBag instance.
     *
     * @template TObjection of Objection
     *
     * @param array<TObjection> $objections
     *
     * @return ObjectionBag<TObjection>
     */
    public function __invoke($objections)
    {
        return new ObjectionBag($objections);
    }
}
