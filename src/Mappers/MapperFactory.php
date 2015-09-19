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
        'Entity'           => Entity::class,
        'Embeddable'       => Embeddable::class,
        'MappedSuperClass' => MappedSuperClass::class
    ];

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public static function create(Mapping $mapping)
    {
        $reflection = new ReflectionClass($mapping->mapFor());

        foreach (static::$types as $type => $class) {
            if ($reflection->implementsInterface($class)) {
                $mapper = __NAMESPACE__ . '\\' . $type . 'Mapper';

                if (class_exists($mapper)) {
                    return new $mapper($mapping);
                }
            }
        }

        throw new MappingException('Your mapped class should implement ' . Entity::class . ', ' . MappedSuperClass::class . ' or ' . Embeddable::class);
    }
}
