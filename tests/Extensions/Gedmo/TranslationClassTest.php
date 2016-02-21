<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Entity;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Translatable\Mapping\Driver\Fluent as TranslatableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TranslationClass;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Translatable;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class TranslationClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $className;

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
        $this->className     = 'TranslationClass';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension = new TranslationClass($this->classMetadata, $this->className);
    }

    public function test_it_should_add_itself_as_an_entity_macro()
    {
        TranslationClass::enable();

        $entity = (new Entity(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo'))
        ));

        $this->assertInstanceOf(
            TranslationClass::class,
            call_user_func([$entity, TranslationClass::MACRO_METHOD], $this->className)
        );
    }

    public function test_can_mark_a_field_as_locale()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'translationClass' => 'TranslationClass',
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
     * @return TranslationClass
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
