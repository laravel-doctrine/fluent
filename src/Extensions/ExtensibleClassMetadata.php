<?php

namespace LaravelDoctrine\Fluent\Extensions;

use Doctrine\ORM\Mapping\ClassMetadata;

class ExtensibleClassMetadata extends ClassMetadata
{
    /**
     * A dictionary of extension metadata mapped to this class.
     *
     * @var array
     */
    public $extensions = [];

    /**
     * Adds the extension configuration.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    public function addExtension($name, array $config)
    {
        $this->extensions[$name] = $config;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getExtension($name)
    {
        if (isset($this->extensions[$name])) {
            return $this->extensions[$name];
        }

        return [];
    }

    /**
     * Merge with current extension configuration, appending new values to old ones.
     *
     * @param string $name
     * @param array  $config
     */
    public function appendExtension($name, array $config = [])
    {
        $merged = array_merge_recursive(
            $this->getExtension($name),
            $config
        );

        $this->addExtension($name, $merged);
    }

    /**
     * Merge with current extension configuration, overwriting with new values.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    public function mergeExtension($name, array $config)
    {
        $this->addExtension($name, array_merge(
            $this->getExtension($name),
            $config
        ));
    }
}
