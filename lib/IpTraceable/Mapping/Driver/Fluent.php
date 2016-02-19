<?php

namespace Gedmo\IpTraceable\Mapping\Driver;

use Gedmo\FluentExtension;

class Fluent extends FluentExtension
{
    const EXTENSION_NAME = 'IpTraceable';

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
}
