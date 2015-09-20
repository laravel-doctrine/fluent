<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\NamingStrategy;
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
     * @param ClassMetadataInfo $metadata
     * @param Fluent            $builder
     * @param NamingStrategy    $namingStrategy
     */
    public function map(ClassMetadataInfo $metadata, Fluent $builder, NamingStrategy $namingStrategy)
    {
        $cm = new ClassMetadataBuilder($metadata);

        $this->setType($cm);
        $builder->setBuilder($cm);
        $builder->setNamingStrategy($namingStrategy);

        $this->mapping->map($builder);

        foreach ($builder->getQueued() as $buildable) {
            $buildable->build();
        }
    }

    /**
     * @param ClassMetadataBuilder $metadata
     */
    protected function setType(ClassMetadataBuilder $metadata)
    {
        // By default nothing has to be done
    }
}
