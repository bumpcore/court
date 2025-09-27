<?php

namespace BumpCore\Court;

/**
 * @template TObjection of Contracts\Objection
 *
 * @implements Contracts\ObjectionCollector<TObjection>
 */
class ObjectionCollector implements Contracts\ObjectionCollector
{
    /**
     * Collected objections.
     *
     * @var array<int, TObjection>
     */
    protected array $objections = [];

    /**
     * The objection factory callable.
     *
     * @var callable(mixed): TObjection
     */
    protected $objectionFactory;

    /**
     * Create a new objection collector instance.
     *
     * @param callable(): TObjection $objectionFactory
     */
    public function __construct(callable $objectionFactory)
    {
        $this->objectionFactory = $objectionFactory;
    }

    /**
     * Raise a new objection.
     *
     * @param mixed $value
     *
     * @return TObjection
     */
    public function newObjection($value)
    {
        $this->objections[] = $objection = $this->createObjection($value);

        return $objection;
    }

    /**
     * Get a closure that raises objections.
     *
     * @return callable(mixed): TObjection
     */
    public function closure()
    {
        return function ($input) {
            return $this->newObjection($input);
        };
    }

    /**
     * Get all collected objections.
     *
     * @return array<int, TObjection>
     */
    public function all(): array
    {
        return $this->objections;
    }

    /**
     * Create a new objection instance.
     *
     * @param mixed $value
     *
     * @return TObjection
     */
    protected function createObjection($value)
    {
        return ($this->objectionFactory)($value);
    }
}
