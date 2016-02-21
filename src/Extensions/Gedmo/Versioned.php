<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Loggable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;

class Versioned implements Buildable
{
    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * Versioned constructor.
     *
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName     = $fieldName;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $config = $this->classMetadata->getExtension(Fluent::EXTENSION_NAME);

        $config['loggable']  = true;
        $config['versioned'] = array_unique(array_merge(
            isset($config['versioned']) ? $config['versioned'] : [],
            [
                $this->fieldName,
            ]
        ));

        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, $config);
    }
}
