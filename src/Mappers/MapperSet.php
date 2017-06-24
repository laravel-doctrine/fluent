<?php

namespace LaravelDoctrine\Fluent\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\EmbeddableMapping;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\MappedSuperClassMapping;
use LaravelDoctrine\Fluent\Mapping;

class MapperSet
{
    /**
     * @var Mapper[]
     */
    protected $mappers = [];

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public function add(Mapping $mapping)
    {
        $mapping->addMapperTo($this);
    }

    /**
     * @param string $className
     *
     * @throws MappingException
     *
     * @return Mapper
     */
    public function getMapperFor($className)
    {
        if (!$this->hasMapperFor($className)) {
            throw new MappingException(sprintf(
                'Class [%s] does not have a mapping configuration. '.
                'Make sure you create a Mapping class that extends either %s, %s or %s. '.
                'If you are using inheritance mapping, remember to create mappings '.
                'for every child of the inheritance tree.',
                $className,
                EntityMapping::class,
                EmbeddableMapping::class,
                MappedSuperClassMapping::class
            ));
        }

        return $this->mappers[$className];
    }

    /**
     * @param string $className
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
     * @return string[]
     */
    public function getClassNames()
    {
        return array_keys($this->mappers);
    }

    /**
     * Add a mapper to the given class.
     *
     * @param string $class
     * @param Mapper $mapper
     */
    public function addMapper($class, Mapper $mapper)
    {
        $this->mappers[$class] = $mapper;
    }
}
