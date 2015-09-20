<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\EmbeddableMapping;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\MappedSuperClassMapping;
use LaravelDoctrine\Fluent\Mapping;

class MapperFactory
{
    /**
     * @var array
     */
    protected static $types = [
        Mapping::ENTITY             => EntityMapper::class,
        Mapping::EMBEDDABLE         => EmbeddableMapper::class,
        Mapping::MAPPED_SUPER_CLASS => MappedSuperClassMapper::class
    ];

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public static function create(Mapping $mapping)
    {
        if (isset(static::$types[$mapping->mapAs()])) {
            return new static::$types[$mapping->mapAs()]($mapping);
        }

        throw new MappingException('Your mapping class should extend ' . EntityMapping::class . ', ' . MappedSuperClassMapping::class . ' or ' . EmbeddableMapping::class);
    }
}
