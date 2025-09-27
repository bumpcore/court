<?php

namespace BumpCore\Court;

/**
 * @template TObjection of Contracts\Objection
 *
 * @implements Contracts\ObjectionBag<TObjection>
 */
class ObjectionBag implements Contracts\ObjectionBag
{
    /**
     * The collected objections.
     *
     * @var array<TObjection>
     */
    protected $objections = [];

    /**
     * Constructor.
     *
     * @param array<TObjection> $objections
     */
    public function __construct($objections = [])
    {
        $this->objections = $objections;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->objections;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->objections);
    }

    /**
     * @inheritDoc
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function merge($objections)
    {
        if ($objections instanceof Contracts\ObjectionBag) {
            $objections = $objections->all();
        }

        $this->objections = array_merge($this->objections, $objections);

        return $this;
    }
}
