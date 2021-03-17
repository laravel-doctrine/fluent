<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreePathSource;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
class TreePathSourceTest extends TestCase
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
     * @var TreePathSource
     */
    private $extension;

    protected function setUp(): void
    {
        $this->fieldName     = 'source';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();

        $this->extension = new TreePathSource($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        TreePathSource::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreePathSource::class,
            call_user_func([$field, TreePathSource::MACRO_METHOD])
        );
    }

    public function test_path_source_must_be_valid_field()
    {
        $this->expectException(InvalidMappingException::class);
        $this->expectExceptionMessage('Tree PathSource field - [source] type is not valid. It can be any of the integer variants, double, float or string in class - foo');

        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'binary', $this->fieldName)->build();
        $this->extension = new TreePathSource($this->classMetadata, $this->fieldName);

        $this->getExtension()->build();
    }

    public function test_can_mark_a_field_as_source()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'path_source' => 'source',
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
     * @return TreePathSource
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
