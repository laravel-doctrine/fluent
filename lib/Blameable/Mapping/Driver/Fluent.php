<?php

namespace Gedmo\Blameable\Mapping\Driver;

use Gedmo\FluentExtension;

class Fluent extends FluentExtension
{
    const EXTENSION_NAME = 'Blameable';

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
}
