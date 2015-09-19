<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Fluent;

final class MappedSuperClassMapper extends AbstractMapper
{
    /**
     * @param ClassMetadataInfo $metadata
     * @param Fluent            $builder
     */
    public function map(ClassMetadataInfo $metadata, Fluent $builder = null)
    {
        $builder = Builder::createMappedSuperClass(
            $this->getBuilder($metadata)
        );

        parent::map($metadata, $builder);
    }

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     * @return bool
     */
    public function isTransient()
    {
        return false;
    }
}
