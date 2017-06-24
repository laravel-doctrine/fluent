<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Translatable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;

class TranslationClass implements Buildable
{
    const MACRO_METHOD = 'translationClass';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $class;

    /**
     * Locale constructor.
     *
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $class
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $class)
    {
        $this->classMetadata = $classMetadata;
        $this->class = $class;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Builder $builder, $class) {
            return new static($builder->getClassMetadata(), $class);
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'translationClass' => $this->class,
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
