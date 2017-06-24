<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class TreeSelfReference implements Buildable
{
    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string
     */
    private $key;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     * @param string                  $key
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName, $key)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName = $fieldName;
        $this->key = $key;
    }

    /**
     * Enable TreeRoot and TreeParent macros.
     *
     * @return void
     */
    public static function enable()
    {
        static::enableRoot();
        static::enableParent();
    }

    /**
     * Enable only the TreeRoot macro.
     *
     * @return void
     */
    public static function enableRoot()
    {
        static::addMacro('treeRoot', 'root');
    }

    /**
     * Enable only the TreeParent macro.
     *
     * @return void
     */
    public static function enableParent()
    {
        static::addMacro('treeParent', 'parent');
    }

    /**
     * @param string $method
     * @param string $key
     *
     * @return void
     */
    protected static function addMacro($method, $key)
    {
        Field::macro($method, function (Field $field) use ($key) {
            $field->nullable();

            return new static($field->getClassMetadata(), $field->getName(), $key);
        });

        ManyToOne::macro($method, function (ManyToOne $relation) use ($key) {
            $relation->nullable();

            return new static($relation->getClassMetadata(), $relation->getRelation(), $key);
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->classMetadata->mergeExtension($this->getExtensionName(), [
            $this->key => $this->fieldName,
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
