<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Fluent;

final class EmbeddableMapper extends AbstractMapper
{
    /**
     * @param ClassMetadataInfo $metadata
     * @param Fluent            $builder
     */
    public function map(ClassMetadataInfo $metadata, Fluent $builder)
    {
        $classMetaDataBuilder = $this->getBuilder($metadata);
        $classMetaDataBuilder->setEmbeddable();

        $builder->setBuilder(
            $classMetaDataBuilder
        );

        parent::map($metadata, $builder);
    }

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @return bool
     */
    public function isTransient()
    {
        return true;
    }
}
