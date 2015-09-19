<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Fluent;
use LogicException;

class Builder extends AbstractBuilder implements Fluent
{
    /**
     * @var array
     */
    protected $pendingFields = [];

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
     * @param ClassMetadataBuilder $builder
     *
     * @return Builder
     */
    public static function createEntity(ClassMetadataBuilder $builder)
    {
        return new static($builder);
    }

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return Builder
     */
    public static function createEmbeddable(ClassMetadataBuilder $builder)
    {
        $builder->setEmbeddable();

        return new static($builder);
    }

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return Builder
     */
    public static function createMappedSuperClass(ClassMetadataBuilder $builder)
    {
        $builder->setMappedSuperClass();

        return new static($builder);
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
}
