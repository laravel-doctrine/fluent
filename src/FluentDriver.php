<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\MappingException;
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
     * @var callable
     */
    protected $fluentFactory;

    /**
     * Initializes a new FileDriver that looks in the given path(s) for mapping
     * documents and operates in the specified operating mode.
     *
     * @param string[]|null $mappings
     * @param string[]|null $paths
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function __construct($mappings, $paths)
    {
        $this->fluentFactory = function (ClassMetadata $metadata) {
            return new Builder(new ClassMetadataBuilder($metadata));
        };

        $this->mappers = new MapperSet();

        if ($mappings !== null) {
            $this->addMappings($mappings);
        }

        if ($paths !== null) {
            $this->addPaths($paths);
        }
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $this->mappers->getMapperFor($className)->map(
            $this->getFluent($metadata)
        );
    }

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @throws MappingException
     *
     * @return string[] The names of all mapped classes known to this driver.
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
        return
            !$this->mappers->hasMapperFor($className) ||
            $this->mappers->getMapperFor($className)->isTransient();
    }

    /**
     * @param string[] $mappings
     */
    public function addMappings(array $mappings = [])
    {
        foreach ($mappings as $class) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Mapping class [{$class}] does not exist");
            }

            $mapping = new $class();

            if (!$mapping instanceof Mapping) {
                throw new InvalidArgumentException("Mapping class [{$class}] should implement ".Mapping::class);
            }

            $this->addMapping($mapping);
        }
    }

    /**
     * @param Mapping $mapping
     *
     * @throws MappingException
     */
    public function addMapping(Mapping $mapping)
    {
        $this->mappers->add($mapping);
    }

    /**
     * @return MapperSet
     */
    public function getMappers()
    {
        return $this->mappers;
    }

    /**
     * Add mappings from an array of folders.
     *
     * @param string[] $paths
     *
     * @throws MappingException
     */
    public function addPaths($paths)
    {
        $includedFiles = [];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($iterator as $file) {
                if ($file->getBasename('.php') == $file->getBasename()) {
                    continue;
                }

                $sourceFile = realpath($file->getPathName());
                require_once $sourceFile;
                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (in_array($sourceFile, $includedFiles) && !$this->mappers->hasMapperFor($className)) {
                $this->addMapping(new $className());
            }
        }
    }

    /**
     * Override the default Fluent factory method with a custom one.
     * Use this to implement your own Fluent builder.
     * The method will receive a ClassMetadata object as its only argument.
     *
     * @param callable $factory
     */
    public function setFluentFactory(callable $factory)
    {
        $this->fluentFactory = $factory;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return Fluent
     */
    protected function getFluent(ClassMetadata $metadata)
    {
        return call_user_func($this->fluentFactory, $metadata);
    }
}
