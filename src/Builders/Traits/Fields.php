<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\DBAL\Types\Types;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;

trait Fields
{
    /**
     * {@inheritdoc}
     */
    public function field($type, $name, callable $callback = null)
    {
        $field = Field::make($this->getBuilder(), $type, $name);

        $this->callbackAndQueue($field, $callback);

        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function string($name, callable $callback = null)
    {
        return $this->field(Types::STRING, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function text($name, callable $callback = null)
    {
        return $this->field(Types::TEXT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function integer($name, callable $callback = null)
    {
        return $this->field(Types::INTEGER, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function smallInteger($name, callable $callback = null)
    {
        return $this->field(Types::SMALLINT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function bigInteger($name, callable $callback = null)
    {
        return $this->field(Types::BIGINT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function guid($name, callable $callback = null)
    {
        return $this->field(Types::GUID, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function blob($name, callable $callback = null)
    {
        return $this->field(Types::BLOB, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function object($name, callable $callback = null)
    {
        return $this->field(Types::OBJECT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function float($name, callable $callback = null)
    {
        return $this->field(Types::FLOAT, $name, $callback)->precision(8)->scale(2);
    }

    /**
     * {@inheritdoc}
     */
    public function decimal($name, callable $callback = null)
    {
        return $this->field(Types::DECIMAL, $name, $callback)->precision(8)->scale(2);
    }

    /**
     * {@inheritdoc}
     */
    public function boolean($name, callable $callback = null)
    {
        return $this->field(Types::BOOLEAN, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function simpleArray($name, callable $callback = null)
    {
        return $this->field(Types::SIMPLE_ARRAY, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonArray($name, callable $callback = null)
    {
        return $this->field(Types::JSON, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function binary($name, callable $callback = null)
    {
        return $this->field(Types::BINARY, $name, $callback)->nullable();
    }

    /**
     * @return \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder
     */
    abstract public function getBuilder();

    /**
     * @param Buildable     $buildable
     * @param callable|null $callback
     */
    abstract protected function callbackAndQueue(Buildable $buildable, callable $callback = null);
}
