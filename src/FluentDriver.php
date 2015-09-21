<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\NamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Mappers\MapperSet;

class FluentDriver implements MappingDriver
{
    /**
     * @var MapperSet
     */
    protected $mappers;

    /**
     * @var Fluent
     */
    protected $builder;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * Initializes a new FileDriver that looks in the given path(s) for mapping
     * documents and operates in the specified operating mode.
     *
     * @param array          $mappings
     * @param NamingStrategy $namingStrategy
     * @param Fluent         $builder
     */
    public function __construct(array $mappings = [], NamingStrategy $namingStrategy = null, Fluent $builder = null)
    {
        $this->mappers        = new MapperSet();
        $this->builder        = $builder ?: new Builder();
        $this->namingStrategy = $namingStrategy ?: new DefaultNamingStrategy();

        $this->addMappings($mappings);
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $this->mappers->getMapperFor($className)->map($metadata, $this->builder, $this->namingStrategy);
    }

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @throws MappingException
     * @return array            The names of all mapped classes known to this driver.
     */
    public function getAllClassNames()
    {
        return $this->mappers->getClassNames();
    }

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @param string $className
     *
     * @return bool
     */
    public function isTransient($className)
    {
        return $this->mappers->getMapperFor($className)->isTransient();
    }

    /**
     * @param array $mappings
     */
    public function addMappings(array $mappings = [])
    {
        foreach ($mappings as $class) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Mapping class [{$class}] does not exist");
            }

            $mapping = new $class;

            if (!$mapping instanceof Mapping) {
                throw new InvalidArgumentException("Mapping class [{$class}] should implement " . Mapping::class);
            }

            $this->addMapping($mapping);
        }
    }

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     * @return void
     */
    public function addMapping(Mapping $mapping)
    {
        $this->mappers->add($mapping);
    }

    /**
     * @return Fluent
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return MapperSet
     */
    public function getMappers()
    {
        return $this->mappers;
    }
}
