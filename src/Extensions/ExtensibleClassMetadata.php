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
     * @param string $name
     * @param array  $configuration
     *
     * @return void
     */
    public function addExtension($name, array $configuration)
    {
        $this->extensions[$name] = $configuration;
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
}
