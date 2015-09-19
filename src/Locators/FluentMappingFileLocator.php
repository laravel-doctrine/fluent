<?php

namespace LaravelDoctrine\Fluent\Locators;

use Doctrine\ORM\Mapping\MappingException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FluentMappingFileLocator
{
    /**
     * Mapping files should end on Mapping.php
     * @var string
     */
    protected $fileExtension = 'Mapping.php';

    /**
     * The paths where to look for mapping files.
     * @var array
     */
    protected $paths = [];

    /**
     * Initializes a new FileDriver that looks in the given path(s) for mapping
     * documents and operates in the specified operating mode.
     *
     * @param array $paths One or multiple paths where mapping documents can be found.
     */
    public function __construct(array $paths = [])
    {
        $this->addPaths($paths);
    }

    /**
     * Appends lookup paths to metadata driver.
     *
     * @param array $paths
     *
     * @return void
     */
    public function addPaths(array $paths)
    {
        $this->paths = array_unique(array_merge($this->paths, $paths));
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames($globalBasename = null)
    {
        $classes = [];

        if (count($this->paths) > 0) {
            foreach ($this->paths as $path) {
                if (!is_dir($path)) {
                    throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
                }

                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    $fileName = $file->getBasename($this->fileExtension);

                    if ($fileName == $file->getBasename() || $fileName == $globalBasename) {
                        continue;
                    }

                    $classes[] = $this->getClassName($file);
                }
            }
        }

        return $classes;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getClassName($file)
    {
        $before = get_declared_classes();
        require_once($file->getPathName());
        $diff  = array_diff(get_declared_classes(), $before);
        $class = reset($diff);

        return $class;
    }
}
