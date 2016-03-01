<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class TreeRoot implements Buildable
{
    const MACRO_METHOD = 'treeRoot';

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName     = $fieldName;
    }

    /**
     * Enable TreeRoot
     */
    public static function enable()
    {
        Field::macro(self::MACRO_METHOD, function (Field $field) {
            $field->nullable();

            return new static($field->getClassMetadata(), $field->getName());
        });

        ManyToOne::macro(self::MACRO_METHOD, function (ManyToOne $relation) {
            $relation->nullable();

            return new static($relation->getClassMetadata(), $relation->getRelation());
        });
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $this->classMetadata->mergeExtension($this->getExtensionName(), [
            'root' => $this->fieldName,
        ]);
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return FluentDriver::EXTENSION_NAME;
    }
}
