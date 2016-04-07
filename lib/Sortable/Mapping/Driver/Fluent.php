<?php

namespace Gedmo\Sortable\Mapping\Driver;

use Gedmo\FluentExtension;

class Fluent extends FluentExtension
{
    const EXTENSION_NAME = 'Sortable';

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
}
