<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Translatable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;

class Locale implements Buildable, Extension, Delay
{
    const MACRO_METHOD = 'locale';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * Locale constructor.
     *
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName = $fieldName;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Builder $builder, $fieldName) {
            return new static($builder->getClassMetadata(), $fieldName);
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        if ($this->classMetadata->hasField($this->fieldName)) {
            throw new InvalidMappingException(
                "Locale field [{$this->fieldName}] should not be mapped as column property in entity - {$this->classMetadata->name}, since it makes no sense"
            );
        }

        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'locale' => $this->fieldName,
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
