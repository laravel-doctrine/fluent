<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Sortable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class SortableGroup implements Buildable
{
    const MACRO_METHOD = 'sortableGroup';

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
     * @return void
     */
    public static function enable()
    {
        Field::macro(self::MACRO_METHOD, function (Field $builder) {
            return new static($builder->getClassMetadata(), $builder->getName());
        });

        ManyToOne::macro(self::MACRO_METHOD, function (ManyToOne $builder) {
            return new static($builder->getClassMetadata(), $builder->getRelation());
        });

        ManyToMany::macro(self::MACRO_METHOD, function (ManyToMany $builder) {
            return new static($builder->getClassMetadata(), $builder->getRelation());
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'groups' => [
                $this->fieldName,
            ],
        ]);
    }
}
