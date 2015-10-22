<?php

namespace LaravelDoctrine\Fluent\Builders;

use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\InheritanceFactory;
use LaravelDoctrine\Fluent\Builders\Overrides\Override;
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
     * @var Buildable[]
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
     * @param array|string $fields
     *
     * @return Primary
     */
    public function primary($fields)
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        $primary = new Primary(
            $this->builder,
            $fields
        );

        $this->queue($primary);

        return $primary;
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
     * @param string        $embeddable
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return Embedded
     */
    public function embed($embeddable, $field = null, callable $callback = null)
    {
        $embedded = new Embedded(
            $this->builder,
            $this->namingStrategy,
            $this->guessSingularField($embeddable, $field),
            $embeddable
        );

        $this->callbackAndQueue($embedded, $callback);

        return $embedded;
    }

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return Override
     */
    public function override($name, callable $callback)
    {
        $override = new Override(
            $this->getBuilder(),
            $this->getNamingStrategy(),
            $name,
            $callback
        );

        $this->queue($override);

        return $override;
    }

    /**
     * @return bool
     */
    public function isEmbeddedClass()
    {
        return $this->builder->getClassMetadata()->isEmbeddedClass;
    }

    /**
     * @return Buildable[]
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
     * @param string $method
     * @param array  $params
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
     * @param callable|null $callback
     *
     * @return LifecycleEvents
     */
    public function events(callable $callback = null)
    {
        $events = new LifecycleEvents($this->builder);

        $this->callbackAndQueue($events, $callback);

        return $events;
    }
}
