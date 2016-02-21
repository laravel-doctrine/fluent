<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreePath;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class TreePathTest extends \PHPUnit_Framework_TestCase
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
     * @var TreePath
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'path';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();

        $this->extension = new TreePath($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        TreePath::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreePath::class,
            call_user_func([$field, TreePath::MACRO_METHOD])
        );
    }

    public function test_can_mark_a_field_as_path()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'path'                       => 'path',
            'path_separator'             => '|',
            'path_append_id'             => null,
            'path_starts_with_separator' => false,
            'path_ends_with_separator'   => true,
        ]);
    }

    public function test_can_mark_a_field_as_path_with_custom_settings()
    {
        $this->getExtension()
             ->separator('/')
             ->appendId(1)
             ->startsWithSeparator(true)
             ->endsWithSeparator(false)
             ->build();

        $this->assertBuildResultIs([
            'path'                       => 'path',
            'path_separator'             => '/',
            'path_append_id'             => 1,
            'path_starts_with_separator' => true,
            'path_ends_with_separator'   => false,
        ]);
    }

    public function test_separator_should_given()
    {
        $this->setExpectedException(InvalidMappingException::class, 'Tree Path field - [path] Separator ||| is invalid. It must be only one character long.');

        $this->getExtension()
             ->separator('|||')
             ->build();

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
     * @return TreePath
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
        return TreeDriver::EXTENSION_NAME;
    }
}
