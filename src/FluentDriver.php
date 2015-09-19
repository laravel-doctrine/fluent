<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Locators\FluentMappingFileLocator;
use LaravelDoctrine\Fluent\Mappers\MapperSet;

class FluentDriver implements MappingDriver
{
    /**
     * @var array
     */
    protected $classCache = [];

    /**
     * @var MapperSet
     */
    protected $mappers;

    /**
     * @var FluentMappingFileLocator
     */
    protected $locator;

    /**
     * @var Fluent
     */
    protected $builder;

    /**
     * Initializes a new FileDriver that looks in the given path(s) for mapping
     * documents and operates in the specified operating mode.
     *
     * @param array  $paths
     * @param Fluent $builder
     */
    public function __construct(array $paths = [], Fluent $builder = null)
    {
        $this->mappers = new MapperSet();
        $this->builder = $builder ?: new Builder();
        $this->locator = new FluentMappingFileLocator($paths);

        $this->loadMappingFilesFromPaths();
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $this->mappers->getMapperFor($className)->map($metadata, $this->builder);
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
     * @return array
     */
    public function getPaths()
    {
        return $this->locator->getPaths();
    }

    /**
     * @param array $paths
     */
    public function addPaths($paths)
    {
        $this->locator->addPaths($paths);

        // We should load these news paths
        $this->loadMappingFilesFromPaths();
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

    /**
     * @throws MappingException
     */
    protected function loadMappingFilesFromPaths()
    {
        foreach ($this->locator->getAllClassNames() as $className) {
            if (class_exists($className)) {
                $mapping = new $className;

                if ($mapping instanceof Mapping) {
                    $this->mappers->add($mapping);
                }
            }
        }
    }
}
