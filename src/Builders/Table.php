<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class Table extends AbstractBuilder
{
    /**
     * @param ClassMetadataBuilder $builder
     * @param string|callable|null $name
     */
    public function __construct(ClassMetadataBuilder $builder, $name = null)
    {
        parent::__construct($builder);

        if (is_callable($name)) {
            $name($this);
        } else {
            $this->setName($name);
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->builder->setTable($name);

        return $this;
    }

    /**
     * @param string $schema
     *
     * @return $this
     */
    public function schema($schema)
    {
        $this->builder->getClassMetadata()->setPrimaryTable(['schema' => $schema]);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        $this->builder->getClassMetadata()->setPrimaryTable(['options' => $options]);

        return $this;
    }
}
