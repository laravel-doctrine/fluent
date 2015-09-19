<?php

namespace tests\Locators;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Locators\FluentMappingFileLocator;

class FluentMappingFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_get_all_class_names()
    {
        $locator = new FluentMappingFileLocator([
            __DIR__ . '/../Stubs/Mappings3'
        ]);

        $this->assertContains(__DIR__ . '/../Stubs/Mappings3', $locator->getPaths());
        $this->assertContains('Tests\Stubs\Mappings3\StubEntity3Mapping', $locator->getAllClassNames());
    }

    public function test_can_not_get_all_class_names_for_invalid_dir()
    {
        $this->setExpectedException(MappingException::class);

        $locator = new FluentMappingFileLocator([
            __DIR__ . '/non/existing/dir'
        ]);

        $locator->getAllClassNames();
    }
}
