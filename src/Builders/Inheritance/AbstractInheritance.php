<?php

namespace LaravelDoctrine\Fluent\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @method $this addDiscriminatorMapClass($name, $class)
 * @method $this setDiscriminatorColumn($column, $type = 'string', $length = 255)
 */
abstract class AbstractInheritance implements Inheritance
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @param ClassMetadataBuilder $builder
     */
    public function __construct(ClassMetadataBuilder $builder)
    {
        $this->builder = $builder;
        $this->setType();
    }

    /**
     * Set inheritance type.
     */
    abstract protected function setType();

    /**
     * Add the discriminator column.
     *
     * @param string $column
     * @param string $type
     * @param int    $length
     *
     * @return Inheritance
     */
    public function column($column, $type = 'string', $length = 255)
    {
        $this->builder->setDiscriminatorColumn($column, $type, $length);

        return $this;
    }

    /**
     * @param string      $name
     * @param string|null $class
     *
     * @return Inheritance
     */
    public function map($name, $class = null)
    {
        if (is_array($name)) {
            foreach ($name as $name => $class) {
                $this->map($name, $class);
            }

            return $this;
        }

        $this->builder->addDiscriminatorMapClass($name, $class);

        return $this;
    }
}
