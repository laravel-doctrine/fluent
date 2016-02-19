<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Blameable;

use Gedmo\Blameable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class Extension extends AbstractTrackingExtension implements Buildable
{
    const MACRO_METHOD = 'blameable';

    /**
     * Enable the extension.
     *
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
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    protected function getExtensionName()
    {
        return Fluent::EXTENSION_NAME;
    }
}
