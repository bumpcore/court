<?php

namespace BumpCore\Court\Factories;

use BumpCore\Court\Objection;

class ObjectionFactory
{
    /**
     * Creates a new Objection instance
     * with the given value.
     *
     * @param mixed $value
     *
     * @return Objection
     */
    public function __invoke($value)
    {
        return new Objection($value);
    }
}
