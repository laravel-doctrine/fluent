<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Timestampable;

use Gedmo\Timestampable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;

class Extension extends AbstractTrackingExtension implements Buildable
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

        Builder::macro('timestamps', function (\LaravelDoctrine\Fluent\Fluent $builder) {
            $builder->dateTime('createdAt')->timestampable()->onCreate();
            $builder->dateTime('updatedAt')->timestampable()->onUpdate();
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
