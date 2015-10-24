<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;

abstract class AbstractMapper implements Mapper
{
    /**
     * @var Mapping
     */
    protected $mapping;

    /**
     * @param Mapping $mapping
     */
    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
        $this->setType($builder->getBuilder());

        $this->mapping->map($builder);

        $builder->build();
    }

    /**
     * @param ClassMetadataBuilder $metadata
     */
    protected function setType(ClassMetadataBuilder $metadata)
    {
        // By default nothing has to be done
    }
}
