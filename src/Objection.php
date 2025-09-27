<?php

namespace BumpCore\Court;

class Objection implements Contracts\Objection
{
    /**
     * The objection value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new objection instance.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the objection value.
     *
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}
