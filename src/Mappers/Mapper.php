<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Fluent;

interface Mapper
{
    /**
     * @param ClassMetadataInfo $metadata
     * @param Fluent            $builder
     * @param NamingStrategy    $namingStrategy
     *
     * @return
     */
    public function map(ClassMetadataInfo $metadata, Fluent $builder, NamingStrategy $namingStrategy);

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @return bool
     */
    public function isTransient();
}
