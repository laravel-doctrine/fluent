<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\SoftDeleteable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;

class SoftDeleteable implements Buildable, Extension
{
    const MACRO_METHOD = 'softDelete';

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
    protected $value;

    /**
     * @var bool
     */
    protected $timeAware = false;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName = $fieldName;
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
     * @param bool $value
     *
     * @return $this
     */
    public function timeAware($value = true)
    {
        $this->timeAware = $value;

        return $this;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        Builder::macro(static::MACRO_METHOD, function (Builder $builder, $fieldName = 'deletedAt', $type = 'dateTime') {
            $builder->{$type}($fieldName)->nullable();

            return new static($builder->getClassMetadata(), $fieldName);
        });

        Field::macro(static::MACRO_METHOD, function (Field $builder) {
            return new static($builder->getClassMetadata(), $builder->getName());
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->classMetadata->addExtension($this->getExtensionName(), [
            'softDeleteable' => true,
            'fieldName'      => $this->fieldName,
            'timeAware'      => $this->timeAware,
        ]);
    }
}
