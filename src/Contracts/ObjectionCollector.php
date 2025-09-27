<?php

namespace BumpCore\Court\Contracts;

/**
 * @template T of Objection
 */
interface ObjectionCollector
{
    /**
     * Get a closure that raises new objection.
     *
     * @return \Closure(mixed): T
     */
    public function closure();

    /**
     * Get all collected objections.
     *
     * @return array<int, T>
     */
    public function all();
}
