<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Embeddable;
use LaravelDoctrine\Fluent\Entity;
use LaravelDoctrine\Fluent\MappedSuperClass;
use LaravelDoctrine\Fluent\Mapping;
use ReflectionClass;

class MapperFactory
{
    /**
     * @var array
     */
    protected static $types = [
        Entity::class           => EntityMapper::class,
        Embeddable::class       => EmbeddableMapper::class,
        MappedSuperClass::class => MappedSuperClassMapper::class
    ];

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public static function create(Mapping $mapping)
    {
        $reflection = new ReflectionClass($mapping->mapFor());

        foreach (static::$types as $interface => $mapper) {
            if ($reflection->implementsInterface($interface)) {
                return new $mapper($mapping);
            }
        }

        throw new MappingException('Your mapped class should implement ' . Entity::class . ', ' . MappedSuperClass::class . ' or ' . Embeddable::class);
    }
}
