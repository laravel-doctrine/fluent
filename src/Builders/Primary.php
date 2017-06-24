<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Buildable;

class Primary implements Buildable
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param ClassMetadataBuilder $builder
     * @param string[]             $fields
     */
    public function __construct(ClassMetadataBuilder $builder, array $fields)
    {
        $this->builder = $builder;
        $this->fields = $fields;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->getClassMetadata()->setIdentifier(
            $this->getFields()
        );
    }

    /**
     * @return string[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
