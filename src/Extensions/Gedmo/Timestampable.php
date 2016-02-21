<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Timestampable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Fluent;

class Timestampable extends AbstractTrackingExtension implements Buildable
{
    const MACRO_METHOD = 'timestampable';

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

        Builder::macro('timestamps', function (Fluent $builder, $createdAt = 'createdAt', $updatedAt = 'updatedAt', $type = 'dateTime') {
            $builder->{$type}($createdAt)->timestampable()->onCreate();
            $builder->{$type}($updatedAt)->timestampable()->onUpdate();
        });
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    protected function getExtensionName()
    {
        return FluentDriver::EXTENSION_NAME;
    }
}
