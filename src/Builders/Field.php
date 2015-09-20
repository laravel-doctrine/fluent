<?php

namespace LaravelDoctrine\Fluent\Builders;

use BadMethodCallException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use LaravelDoctrine\Fluent\Buildable;

/**
 * @method $this unique($flag = true)
 * @method $this nullable($flag = true)
 * @method $this length($length)
 * @method $this columnName($column)
 * @method $this precision($precision)
 * @method $this scale($scale)
 * @method $this default($default)
 * @method $this columnDefinition($def)
 */
class Field implements Buildable
{
    /**
     * @var FieldBuilder
     */
    protected $builder;

    /**
     * Protected constructor to force usage of factory method
     *
     * @param FieldBuilder $builder
     */
    protected function __construct(FieldBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param ClassMetadataBuilder $builder
     * @param                      $type
     * @param                      $name
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return Field
     */
    public static function make(ClassMetadataBuilder $builder, $type, $name)
    {
        $type = Type::getType($type);

        $field = $builder->createField($name, $type->getName());

        return new static(
            $field
        );
    }

    /**
     * @param $columnName
     *
     * @return $this
     */
    public function name($columnName)
    {
        $this->columnName($columnName);

        return $this;
    }

    /**
     * @return Field
     */
    public function autoIncrement()
    {
        $this->generatedValue('AUTO');

        return $this;
    }

    /**
     * @param string $strategy
     *
     * @return Field
     */
    public function generatedValue($strategy)
    {
        $this->builder->generatedValue($strategy);

        return $this;
    }

    /**
     * @return Field
     */
    public function unsigned()
    {
        $this->builder->option('unsigned', true);

        return $this;
    }

    /**
     * @param $default
     *
     * @return Field
     */
    public function setDefault($default)
    {
        $this->builder->option('default', $default);

        return $this;
    }

    /**
     * @param $fixed
     *
     * @return Field
     */
    public function fixed($fixed)
    {
        $this->builder->option('fixed', $fixed);

        return $this;
    }

    /**
     * @param $comment
     *
     * @return Field
     */
    public function comment($comment)
    {
        $this->builder->option('comment', $comment);

        return $this;
    }

    /**
     * @param $collation
     *
     * @return Field
     */
    public function collation($collation)
    {
        $this->builder->option('collation', $collation);

        return $this;
    }

    /**
     * @return Field
     */
    public function primary()
    {
        $this->builder->makePrimaryKey();

        return $this;
    }

    /**
     * @return Field
     */
    public function build()
    {
        $this->builder->build();

        return $this;
    }

    /**
     * @return FieldBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Magic call method works as a proxy for the Doctrine FieldBuilder
     *
     * @param string $method
     * @param array  $args
     *
     * @throws BadMethodCallException
     * @return $this
     */
    public function __call($method, $args)
    {
        // Work around reserved keywords
        if ($method === 'default') {
            return call_user_func_array([$this, 'setDefault'], $args);
        }

        if (method_exists($this->getBuilder(), $method)) {
            call_user_func_array([$this->getBuilder(), $method], $args);

            return $this;
        }

        throw new BadMethodCallException("FieldBuilder method [{$method}] does not exist.");
    }
}
