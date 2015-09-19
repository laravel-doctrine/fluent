<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Embeddable;
use LaravelDoctrine\Fluent\Entity;
use LaravelDoctrine\Fluent\Mapping;

class MapperSet
{
    /**
     * @type Mapper[]
     */
    protected $mappers = [];

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public function add(Mapping $mapping)
    {
        if (!$this->hasMapperFor($mapping->mapFor())) {
            $this->mappers[$mapping->mapFor()] = MapperFactory::create($mapping);
        }
    }

    /**
     * @param $className
     *
     * @throws MappingException
     * @return Mapper
     */
    public function getMapperFor($className)
    {
        if (!$this->hasMapperFor($className)) {
            throw new MappingException(
                "Class [$className] does not have a mapping configuration. " .
                "Make sure you create a Mapping class for it that extends " . Mapping::class . " and make sure your entity extends either " .
                Entity::class . " or " . Embeddable::class . ". " .
                "If you are using inheritance mapping, remember to create mappings for " .
                "every child of the inheritance tree."
            );
        }

        return $this->mappers[$className];
    }

    /**
     * @param $className
     *
     * @return bool
     */
    public function hasMapperFor($className)
    {
        return isset($this->mappers[$className]);
    }

    /**
     * @return bool
     */
    public function hasMappers()
    {
        return count($this->mappers) > 0;
    }

    /**
     * @return Mapper[]
     */
    public function getMappers()
    {
        return $this->mappers;
    }

    /**
     * @return array
     */
    public function getClassNames()
    {
        return array_keys($this->mappers);
    }
}
