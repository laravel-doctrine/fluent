<?php

namespace Gedmo\SoftDeleteable\Mapping\Driver;

use Gedmo\FluentExtension;

class Fluent extends FluentExtension
{
    const EXTENSION_NAME = 'softDeleteable';

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
}
