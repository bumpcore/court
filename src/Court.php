<?php

namespace BumpCore\Court;

use BumpCore\Court\Factories\ObjectionCollectorFactory;

/**
 * @template TObjection of Contracts\Objection
 * @template TObjectionBag of Contracts\ObjectionBag<TObjection>
 * @template TObjectionCollector of Contracts\ObjectionCollector<TObjection>
 *
 * @implements Contracts\Court<TObjection, TObjectionBag, TObjectionCollector>
 */
class Court implements Contracts\Court
{
    /**
     * The objection factory callable.
     *
     * @var null|callable(mixed): TObjection
     */
    protected $objectionFactory = null;

    /**
     * The objection bag factory callable.
     *
     * @var null|callable(): TObjectionBag
     */
    protected $objectionBagFactory = null;

    /**
     * The objection collector factory callable.
     *
     * @var null|callable(): TObjectionCollector
     */
    protected $objectionCollectorFactory = null;

    /**
     * The guard resolver callable.
     *
     * @var null|callable(mixed): (callable(mixed,mixed): void)
     */
    protected $guardResolver = null;

    /**
     * Guards to be run.
     *
     * @var array<int, mixed>
     */
    protected array $guards = [];

    /**
     * The subject under scrutiny.
     *
     * @var mixed
     */
    protected $subject;

    /**
     * Create a new court instance.
     *
     * @param mixed $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Create a new court instance.
     *
     * @param mixed $subject
     *
     * @return static<TObjection, TObjectionBag, TObjectionCollector>
     */
    public static function of($subject)
    {
        /**
         * @var static<TObjection, TObjectionBag, TObjectionCollector>
         */
        return new static($subject);
    }

    /**
     * @inheritDoc
     */
    public function guards($guards)
    {
        $this->guards = array_merge(
            $this->guards,
            is_array($guards) ? $guards : func_get_args()
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setObjectionBagFactory(callable $factory)
    {
        $this->objectionBagFactory = $factory;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setObjectionFactory(callable $factory)
    {
        $this->objectionFactory = $factory;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGuardResolver(callable $resolver)
    {
        $this->guardResolver = $resolver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setObjectionCollectorFactory(callable $factory)
    {
        $this->objectionCollectorFactory = $factory;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return TObjectionBag
     */
    public function verdict()
    {
        $runner = new GuardRunner(
            $this->guardResolver ?? Factory::getGuardResolver()
        );

        $collector = $this->createObjectionCollector();

        foreach ($this->guards as $guard) {
            $runner->run($guard, $this->subject, $collector);
        }

        /**
         * @var TObjectionBag
         */
        return ($this->objectionBagFactory ?? Factory::getObjectionBagFactory())(
            $collector->all()
        );
    }

    /**
     * Create the objection collector instance.
     *
     * @return TObjectionCollector
     */
    protected function createObjectionCollector()
    {
        if ($factory = $this->objectionCollectorFactory ?? Factory::getObjectionCollectorFactory()) {
            /**
             * @var TObjectionCollector
             */
            return $factory();
        }

        /**
         * @var TObjectionCollector
         */
        return (new ObjectionCollectorFactory(
            $this->objectionFactory ?? Factory::getObjectionFactory()
        ))();
    }
}
