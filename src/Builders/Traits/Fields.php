<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\DBAL\Types\Type;
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
        return $this->field(Type::STRING, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function text($name, callable $callback = null)
    {
        return $this->field(Type::TEXT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function integer($name, callable $callback = null)
    {
        return $this->field(Type::INTEGER, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function smallInteger($name, callable $callback = null)
    {
        return $this->field(Type::SMALLINT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function bigInteger($name, callable $callback = null)
    {
        return $this->field(Type::BIGINT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function guid($name, callable $callback = null)
    {
        return $this->field(Type::GUID, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function blob($name, callable $callback = null)
    {
        return $this->field(Type::BLOB, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function object($name, callable $callback = null)
    {
        return $this->field(Type::OBJECT, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function float($name, callable $callback = null)
    {
        return $this->field(Type::FLOAT, $name, $callback)->precision(8)->scale(2);
    }

    /**
     * {@inheritdoc}
     */
    public function decimal($name, callable $callback = null)
    {
        return $this->field(Type::DECIMAL, $name, $callback)->precision(8)->scale(2);
    }

    /**
     * {@inheritdoc}
     */
    public function boolean($name, callable $callback = null)
    {
        return $this->field(Type::BOOLEAN, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function simpleArray($name, callable $callback = null)
    {
        return $this->field(Type::SIMPLE_ARRAY, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonArray($name, callable $callback = null)
    {
        return $this->field(Type::JSON_ARRAY, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function binary($name, callable $callback = null)
    {
        return $this->field(Type::BINARY, $name, $callback)->nullable();
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
