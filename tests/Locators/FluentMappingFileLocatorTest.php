<?php

namespace tests\Locators;

use LaravelDoctrine\Fluent\Locators\FluentMappingFileLocator;

class FluentMappingFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_get_all_class_names()
    {
        $locator = new FluentMappingFileLocator([
            __DIR__ . '/../Stubs/Mappings3'
        ]);

        $this->assertContains(__DIR__ . '/../Stubs/Mappings3', $locator->getPaths());
        $this->assertContains('Tests\Stubs\Mappings2\StubEntity3Mapping', $locator->getAllClassNames());
    }
}
