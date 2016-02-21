<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Translatable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Entity;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;

class Translatable implements Buildable
{
    const MACRO_METHOD = 'translatable';

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string|null
     */
    protected static $translationClass;

    /**
     * @var string|null
     */
    protected static $locale;

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
     * @return null|string
     */
    public static function getLocale()
    {
        return self::$locale;
    }

    /**
     * @return null|string
     */
    public static function getTranslationClass()
    {
        return self::$translationClass;
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

    /**
     * @return void
     */
    public static function enable()
    {
        Field::macro(static::MACRO_METHOD, function (Field $builder) {
            return new static($builder->getClassMetadata(), $builder->getName());
        });

        Field::macro('locale', function (Field $field) {
            self::locale($field->getName());
        });

        Entity::macro('translationClass', function (Entity $entity, $class) {
            self::translationClass($class);
        });
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $extension = $this->classMetadata->getExtension($this->getExtensionName());

        if ($class = self::getTranslationClass()) {
            $extension['translationClass'] = $class;
        }

        if ($locale = self::getLocale()) {
            $extension['locale'] = $locale;
        }

        $extension['fields'][] = $this->fieldName;

        $this->classMetadata->addExtension($this->getExtensionName(), $extension);
    }

    /**
     * @param  string $translationClass
     * @return $this
     */
    public static function translationClass($translationClass)
    {
        static::$translationClass = $translationClass;
    }

    /**
     * @param  string       $locale
     * @return Translatable
     */
    public static function locale($locale)
    {
        static::$locale = $locale;
    }
}
