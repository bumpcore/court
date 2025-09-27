<?php

namespace BumpCore\Court\Contracts;

/**
 * @template TObjection of Objection
 * @template TObjectionBag of ObjectionBag<TObjection>
 * @template TObjectionCollector of ObjectionCollector<TObjection>
 */
interface Court
{
    /**
     * Set the guards to be used in the court.
     *
     * @param mixed $guards
     *
     * @return $this
     */
    public function guards($guards);

    /**
     * Run the court and return the verdict (objections).
     *
     * @return TObjectionBag
     */
    public function verdict();

    /**
     * Set the objection factory callable.
     *
     * @param callable(mixed): TObjection $factory
     *
     * @return $this
     */
    public function setObjectionFactory(callable $factory);

    /**
     * Set the objection bag factory callable.
     *
     * @param callable(): TObjectionBag $factory
     *
     * @return $this
     */
    public function setObjectionBagFactory(callable $factory);

    /**
     * Set the guard resolver callable.
     *
     * @param callable(mixed): (callable(mixed,mixed): void) $resolver
     *
     * @return $this
     */
    public function setGuardResolver(callable $resolver);

    /**
     * Set the objection collector factory callable.
     *
     * @param callable(): TObjectionCollector $factory
     *
     * @return $this
     */
    public function setObjectionCollectorFactory(callable $factory);
}
