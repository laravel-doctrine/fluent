<?php

namespace Gedmo\Loggable\Mapping\Driver;

use Gedmo\FluentExtension;

class Fluent extends FluentExtension
{
    const EXTENSION_NAME = 'Loggable';

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
}
