<?php

namespace BumpCore\Court\Contracts;

/**
 * @template TObjection of Objection
 */
interface ObjectionBag
{
    /**
     * Get all collected objections.
     *
     * @return array<TObjection>
     */
    public function all(): array;

    /**
     * Merge another set of objections into this bag.
     *
     * @param array<TObjection>|ObjectionBag<TObjection> $objections
     *
     * @return $this
     */
    public function merge($objections);

    /**
     * Whether the bag is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Whether the bag is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool;
}
