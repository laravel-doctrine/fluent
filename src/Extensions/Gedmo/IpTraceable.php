<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\IpTraceable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;

class IpTraceable extends AbstractTrackingExtension implements Buildable
{
    const MACRO_METHOD = 'ipTraceable';

    /**
     * @return void
     */
    public static function enable()
    {
        Field::macro(static::MACRO_METHOD, function (Field $builder) {
            return new static($builder->getClassMetadata(), $builder->getName());
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
