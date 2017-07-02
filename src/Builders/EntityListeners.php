<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;

/**
 * @method EntityListeners preRemove(string $listener, string $method = null)
 * @method EntityListeners postRemove(string $listener, string $method = null)
 * @method EntityListeners prePersist(string $listener, string $method = null)
 * @method EntityListeners postPersist(string $listener, string $method = null)
 * @method EntityListeners preUpdate(string $listener, string $method = null)
 * @method EntityListeners postUpdate(string $listener, string $method = null)
 * @method EntityListeners postLoad(string $listener, string $method = null)
 * @method EntityListeners loadClassMetadata(string $listener, string $method = null)
 * @method EntityListeners onClassMetadataNotFound(string $listener, string $method = null)
 * @method EntityListeners preFlush(string $listener, string $method = null)
 * @method EntityListeners onFlush(string $listener, string $method = null)
 * @method EntityListeners postFlush(string $listener, string $method = null)
 * @method EntityListeners onClear(string $listener, string $method = null)
 */
class EntityListeners implements Buildable
{
    /**
     * @var ClassMetadataBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $events = [
        Events::preRemove               => [],
        Events::postRemove              => [],
        Events::prePersist              => [],
        Events::postPersist             => [],
        Events::preUpdate               => [],
        Events::postUpdate              => [],
        Events::postLoad                => [],
        Events::loadClassMetadata       => [],
        Events::onClassMetadataNotFound => [],
        Events::preFlush                => [],
        Events::onFlush                 => [],
        Events::postFlush               => [],
        Events::onClear                 => [],
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
     * @param string $event
     * @param array  $args
     *
     * @throws InvalidArgumentException
     *
     * @return LifecycleEvents
     */
    public function __call($event, $args)
    {
        if (array_key_exists($event, $this->events)) {
            array_unshift($args, $event);

            return call_user_func_array([$this, 'add'], $args);
        }

        throw new InvalidArgumentException('Fluent builder method ['.$event.'] does not exist');
    }

    /**
     * @param string      $event
     * @param string      $class
     * @param string|null $method
     *
     * @return EntityListeners
     */
    private function add($event, $class, $method = null)
    {
        $this->events[$event][] = [
            'class'  => $class,
            'method' => $method ?: $event,
        ];

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->builder->getClassMetadata()->addEntityListener($event, $listener['class'], $listener['method']);
            }
        }
    }
}
