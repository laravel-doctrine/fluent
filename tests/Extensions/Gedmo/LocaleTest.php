<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Translatable\Mapping\Driver\Fluent as TranslatableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Locale;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Translatable;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var Translatable
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'locale';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'locale')->build();

        $this->extension = new Locale($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        Locale::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'string', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            Locale::class,
            call_user_func([$field, Locale::MACRO_METHOD])
        );
    }

    public function test_can_mark_a_field_as_locale()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'locale' => 'locale',
        ]);
    }

    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertBuildResultIs(array $expected)
    {
        $this->assertEquals($expected, $this->classMetadata->getExtension(
            $this->getExtensionName()
        ));
    }

    /**
     * @return Locale
     */
    protected function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TranslatableDriver::EXTENSION_NAME;
    }
}
