<?php

namespace BumpCore\Court;

use BumpCore\Court\Factories\GuardResolver;
use BumpCore\Court\Factories\ObjectionBagFactory;
use BumpCore\Court\Factories\ObjectionFactory;

/**
 * @template TObjection of Contracts\Objection
 * @template TObjectionBag of Contracts\ObjectionBag<TObjection>
 * @template TObjectionCollector of Contracts\ObjectionCollector<TObjection>
 */
class Factory
{
    /**
     * The objection factory callable.
     *
     * @var null|callable(mixed): TObjection
     */
    protected static $objectionFactory = null;

    /**
     * The objection bag factory callable.
     *
     * @var null|callable(array<TObjection>): TObjectionBag
     */
    protected static $objectionBagFactory = null;

    /**
     * The objection collector factory callable.
     *
     * @var null|callable(): TObjectionCollector
     */
    protected static $objectionCollectorFactory;

    /**
     * The guard resolver callable.
     *
     * @var null|callable(mixed): (callable(mixed, TObjectionCollector): void)
     */
    protected static $guardResolver;

    /**
     * Get the objection factory callable.
     *
     * @return callable(mixed): TObjection
     */
    public static function getObjectionFactory()
    {
        /**
         * @var callable(mixed): TObjection
         */
        return self::$objectionFactory ?? new ObjectionFactory();
    }

    /**
     * Get the objection bag factory callable.
     *
     * @return callable(array<TObjection>): TObjectionBag
     */
    public static function getObjectionBagFactory()
    {
        /**
         * @var callable(array<TObjection>): TObjectionBag
         */
        return self::$objectionBagFactory ?? new ObjectionBagFactory();
    }

    /**
     * Get the objection collector factory callable.
     *
     * @return null|callable(): TObjectionCollector
     */
    public static function getObjectionCollectorFactory()
    {
        return self::$objectionCollectorFactory;
    }

    /**
     * Get the guard resolver callable.
     *
     * @return callable(mixed): (callable(mixed, TObjectionCollector): void)
     */
    public static function getGuardResolver()
    {
        return self::$guardResolver ?? new GuardResolver();
    }

    /**
     * Set the objection factory callable.
     *
     * @param callable(mixed): TObjection $factory
     *
     * @return void
     */
    public static function setObjectionFactory(callable $factory)
    {
        self::$objectionFactory = $factory;
    }

    /**
     * Set the objection bag factory callable.
     *
     * @param callable(array<TObjection>): TObjectionBag $factory
     *
     * @return void
     */
    public static function setObjectionBagFactory(callable $factory)
    {
        self::$objectionBagFactory = $factory;
    }

    /**
     * Set the objection collector factory callable.
     *
     * @param callable(): TObjectionCollector $factory
     *
     * @return void
     */
    public static function setObjectionCollectorFactory(callable $factory)
    {
        self::$objectionCollectorFactory = $factory;
    }

    /**
     * Set the guard resolver callable.
     *
     * @param callable(mixed): (callable(mixed, TObjectionCollector): void) $factory
     *
     * @return void
     */
    public static function setGuardResolver(callable $factory)
    {
        self::$guardResolver = $factory;
    }
}
