<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;

class Field
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
     * @return $this
     */
    public function nullable()
    {
        $this->builder->nullable();

        return $this;
    }

    /**
     * @param $column
     *
     * @return $this
     */
    public function setColumnName($column)
    {
        $this->builder->columnName($column);

        return $this;
    }

    /**
     * @return $this
     */
    public function autoIncrement()
    {
        $this->generatedValue('AUTO');

        return $this;
    }

    /**
     * @param string $strategy
     *
     * @return $this
     */
    public function generatedValue($strategy)
    {
        $this->builder->generatedValue($strategy);

        return $this;
    }

    /**
     * @return $this
     */
    public function unsigned()
    {
        $this->builder->option('unsigned', true);

        return $this;
    }

    /**
     * @return $this
     */
    public function primary()
    {
        $this->builder->makePrimaryKey();

        return $this;
    }

    /**
     * @return $this
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
}
