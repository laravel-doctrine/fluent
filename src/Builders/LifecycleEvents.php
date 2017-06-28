<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;

/**
 * @method LifecycleEvents preRemove(string $method)
 * @method LifecycleEvents postRemove(string $method)
 * @method LifecycleEvents prePersist(string $method)
 * @method LifecycleEvents postPersist(string $method)
 * @method LifecycleEvents preUpdate(string $method)
 * @method LifecycleEvents postUpdate(string $method)
 * @method LifecycleEvents postLoad(string $method)
 * @method LifecycleEvents preFlush(string $method)
 */
class LifecycleEvents implements Buildable
{
    /**
     * @var ClassMetadataBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $events = [
        Events::preRemove   => [],
        Events::postRemove  => [],
        Events::prePersist  => [],
        Events::postPersist => [],
        Events::preUpdate   => [],
        Events::postUpdate  => [],
        Events::postLoad    => [],
        Events::preFlush    => [],
    ];

    /**
     * LifecycleEvents constructor.
     *
     * @param ClassMetadataBuilder $builder
     */
    public function __construct(ClassMetadataBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Magically call all methods that match an event name.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws InvalidArgumentException
     *
     * @return LifecycleEvents
     */
    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->events)) {
            array_unshift($args, $method);

            return call_user_func_array([$this, 'add'], $args);
        }

        throw new InvalidArgumentException('Fluent builder method ['.$method.'] does not exist');
    }

    /**
     * @param string $event
     * @param string $method
     *
     * @return LifecycleEvents
     */
    private function add($event, $method)
    {
        $this->events[$event][] = $method;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        foreach ($this->events as $event => $methods) {
            foreach ($methods as $method) {
                $this->builder->addLifecycleEvent($method, $event);
            }
        }
    }
}
