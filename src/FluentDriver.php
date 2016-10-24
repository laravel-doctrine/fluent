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
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getBasename('.php') == $file->getBasename()) {
                    continue;
                }

                $sourceFile = realpath($file->getPathName());
                $className = $this->getClassFromFile($sourceFile);
                $this->addMapping(new $className());
            }
        }
    }

    /**
     * Get the FQN of a class from a source file.
     *
     * @param $pathToFile
     *
     * @return string
     */
    private function getClassFromFile($pathToFile)
    {
        //http://jarretbyrne.com/2015/06/197/
        //Grab the contents of the file
        $contents = file_get_contents($pathToFile);

        //Start with a blank namespace and class
        $namespace = $class = '';

        //Set helper values to know that we have found the namespace/class token and need to collect the string values after them
        $getting_namespace = $getting_class = false;

        //Go through each token and evaluate it as necessary
        foreach (token_get_all($contents) as $token) {

            //If this token is the namespace declaring, then flag that the next tokens will be the namespace name
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $getting_namespace = true;
            }

            //If this token is the class declaring, then flag that the next tokens will be the class name
            if (is_array($token) && $token[0] == T_CLASS) {
                $getting_class = true;
            }

            //While we're grabbing the namespace name...
            if ($getting_namespace === true) {

                //If the token is a string or the namespace separator...
                if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {

                    //Append the token's value to the name of the namespace
                    $namespace .= $token[1];
                } elseif ($token === ';') {

                    //If the token is the semicolon, then we're done with the namespace declaration
                    $getting_namespace = false;
                }
            }

            //While we're grabbing the class name...
            if ($getting_class === true) {

                //If the token is a string, it's the name of the class
                if (is_array($token) && $token[0] == T_STRING) {

                    //Store the token's value as the class name
                    $class = $token[1];

                    //Got what we need, stope here
                    break;
                }
            }
        }

        //Build the fully-qualified class name and return it
        return $namespace ? $namespace.'\\'.$class : $class;
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
