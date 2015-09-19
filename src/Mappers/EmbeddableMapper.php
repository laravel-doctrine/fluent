<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

final class EmbeddableMapper extends AbstractMapper
{
    /**
     * @param ClassMetadataBuilder $metadata
     */
    public function setType(ClassMetadataBuilder $metadata)
    {
        $metadata->setEmbeddable();
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
