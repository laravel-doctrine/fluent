<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
     * @param ClassMetadata $metadata
     * @param Fluent        $builder
     */
    public function map(ClassMetadata $metadata, Fluent $builder = null)
    {
        if ($builder) {
            $this->mapping->map(
                $builder
            );

            // Build all pending fields
            foreach ($builder->getPendingFields() as $field) {
                $field->build();
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return ClassMetadataBuilder
     */
    protected function getBuilder(ClassMetadataInfo $metadata)
    {
        return new ClassMetadataBuilder($metadata);
    }
}
