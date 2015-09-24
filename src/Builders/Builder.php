<?php

namespace LaravelDoctrine\Fluent\Builders;

use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\InheritanceFactory;
use LaravelDoctrine\Fluent\Builders\Traits\Fields;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Builders\Traits\Relations;
use LaravelDoctrine\Fluent\Fluent;
use LogicException;

/**
 * @method $this array($name, callable $callback = null)
 */
class Builder extends AbstractBuilder implements Fluent
{
    use Fields, Relations, Macroable;

    /**
     * @var array|Buildable[]
     */
    protected $queued = [];

    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return Table
     */
    public function table($name, callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException();
        }

        $table = new Table($this->builder);

        if (is_callable($name)) {
            $name($table);
        } else {
            $table->setName($name);
        }

        if (is_callable($callback)) {
            $callback($table);
        }

        return $table;
    }

    /**
     * @param callable|null $callback
     *
     * @return Entity
     */
    public function entity(callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException();
        }

        $entity = new Entity($this->builder);

        if (is_callable($callback)) {
            $callback($entity);
        }

        return $entity;
    }

    /**
     * @param string        $type
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function inheritance($type, callable $callback = null)
    {
        $inheritance = InheritanceFactory::create($type, $this->builder);

        if (is_callable($callback)) {
            $callback($inheritance);
        }

        return $inheritance;
    }

    /**
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function singleTableInheritance(callable $callback = null)
    {
        return $this->inheritance(Inheritance::SINGLE, $callback);
    }

    /**
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function joinedTableInheritance(callable $callback = null)
    {
        return $this->inheritance(Inheritance::JOINED, $callback);
    }

    /**
     * @param array|string $columns
     *
     * @return Index
     */
    public function index($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        $index = new Index(
            $this->builder,
            $columns
        );

        $this->queue($index);

        return $index;
    }

    /**
     * @param array|string $columns
     *
     * @return UniqueConstraint
     */
    public function unique($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        $unique = new UniqueConstraint(
            $this->builder,
            $columns
        );

        $this->queue($unique);

        return $unique;
    }

    /**
     * @param string        $field
     * @param string        $embeddable
     * @param callable|null $callback
     *
     * @return Embedded
     */
    public function embed($field, $embeddable, callable $callback = null)
    {
        $embedded = new Embedded(
            $this->builder,
            $this->namingStrategy,
            $field,
            $embeddable
        );

        $this->callbackAndQueue($embedded, $callback);

        return $embedded;
    }

    /**
     * @return bool
     */
    public function isEmbeddedClass()
    {
        return $this->builder->getClassMetadata()->isEmbeddedClass;
    }

    /**
     * @return array|Buildable[]
     */
    public function getQueued()
    {
        return $this->queued;
    }

    /**
     * @param Buildable $buildable
     */
    protected function queue(Buildable $buildable)
    {
        $this->queued[] = $buildable;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        // Workaround for reserved keywords
        if ($method === 'array') {
            return call_user_func_array([$this, 'setArray'], $params);
        }

        if ($this->hasMacro($method)) {
            return $this->callMacro($method, $params);
        }

        throw new InvalidArgumentException('Fluent builder method [' . $method . '] does not exist');
    }

    /**
     * @param Buildable     $buildable
     * @param callable|null $callback
     */
    protected function callbackAndQueue(Buildable $buildable, callable $callback = null)
    {
        if (is_callable($callback)) {
            $callback($buildable);
        }

        $this->queue($buildable);
    }

    /**
     * @return LifecycleEvents
     */
    public function events()
    {
        $events = new LifecycleEvents($this->builder);

        $this->queue($events);

        return $events;
    }
}
