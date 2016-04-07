<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Index;
use LaravelDoctrine\Fluent\Builders\Primary;
use LaravelDoctrine\Fluent\Builders\UniqueConstraint;

trait Constraints
{
    /**
     * {@inheritdoc}
     */
    public function index($columns)
    {
        return $this->constraint(
            Index::class,
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function primary($fields)
    {
        return $this->constraint(
            Primary::class,
            is_array($fields) ? $fields : func_get_args()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unique($columns)
    {
        return $this->constraint(
            UniqueConstraint::class,
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * @param string $class
     * @param array  $columns
     *
     * @return mixed
     */
    protected function constraint($class, array $columns)
    {
        $constraint = new $class($this->getBuilder(), $columns);

        $this->queue($constraint);

        return $constraint;
    }

    /**
     * @return \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder
     */
    abstract public function getBuilder();

    /**
     * @param Buildable $buildable
     */
    abstract protected function queue(Buildable $buildable);
}
