<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\ORM\Mapping\MappingException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;

/**
 * Returns an array of Mapping objects found on the given paths.
 *
 * @param string[] $paths
 * @param string   $fileExtension
 *
 * @throws MappingException
 *
 * @return Mapping[]
 */
function mappingsFrom(array $paths, $fileExtension = '.php')
{
    $includedFiles = [];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
        }

        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/^.+'.preg_quote($fileExtension, '/').'$/i',
            RecursiveRegexIterator::GET_MATCH
        );

        foreach ($iterator as $file) {
            $sourceFile = $file[0];

            if (!preg_match('(^phar:)i', $sourceFile)) {
                $sourceFile = realpath($sourceFile);
            }

            require_once $sourceFile;

            $includedFiles[$sourceFile] = true;
        }
    }

    $mappings = [];
    $declared = get_declared_classes();
    foreach ($declared as $className) {
        $rc = new ReflectionClass($className);
        $sourceFile = $rc->getFileName();
        if ($sourceFile === false || !array_key_exists($sourceFile, $includedFiles)) {
            continue;
        }

        if ($rc->isAbstract() || $rc->isInterface()) {
            continue;
        }

        if (!$rc->implementsInterface(Mapping::class)) {
            continue;
        }

        $mappings[] = $rc->newInstanceWithoutConstructor();
    }

    return $mappings;
}
