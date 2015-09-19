<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Fluent;
use LogicException;

class Builder extends AbstractBuilder implements Fluent
{
    /**
     * @var array
     */
    protected $pendingFields = [];

    /**
     * @var array
     */
    protected $customMethods = [];

    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return Table
     */
    public function table($name, callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException;
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
            throw new LogicException;
        }

        $entity = new Entity($this->builder);

        if (is_callable($callback)) {
            $callback($entity);
        }

        return $entity;
    }

    /**
     * @param          $type
     * @param          $name
     * @param callable $callback
     *
     * @return Field
     */
    public function field($type, $name, callable $callback = null)
    {
        $field = Field::make($this->builder, $type, $name);

        if (is_callable($callback)) {
            $callback($field);
        }

        $this->addPendingField($field);

        return $field;
    }

    /**
     * @param               $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function increments($name, callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException;
        }

        $field = $this->field(Type::INTEGER, $name, $callback);

        $field->primary()->unsigned()->autoIncrement();

        return $field;
    }

    /**
     * @param          $name
     * @param callable $callback
     *
     * @return Field
     */
    public function string($name, callable $callback = null)
    {
        return $this->field(Type::STRING, $name, $callback);
    }

    /**
     * @return bool
     */
    public function isEmbeddedClass()
    {
        return $this->builder->getClassMetadata()->isEmbeddedClass;
    }

    /**
     * @return array
     */
    public function getPendingFields()
    {
        return $this->pendingFields;
    }

    /**
     * @param $field
     */
    protected function addPendingField($field)
    {
        $this->pendingFields[] = $field;
    }

    /**
     * @param string        $method
     * @param callable|null $callback
     */
    public function extend($method, callable $callback = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Fluent builder should be extended with a closure argument, none given');
        }

        $this->customMethods[$method] = $callback;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (isset($this->customMethods[$method])) {

            // Add builder as first closure param, append the given params
            array_unshift($params, $this);

            return call_user_func_array($this->customMethods[$method], $params);
        }

        throw new InvalidArgumentException('Fluent builder method [' . $method . '] does not exist');
    }
}
