<?php

namespace Tests\Extensions\Gedmo\Sortable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Sortable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable\SortablePosition;

class SortablePositionTest extends \PHPUnit_Framework_TestCase
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
     * @var SortablePosition
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'position';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new SortablePosition($this->classMetadata, $this->fieldName, 'name');
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        SortablePosition::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')
        ), 'string', $this->fieldName)->build();

        $this->assertInstanceOf(
            SortablePosition::class,
            call_user_func([$field, SortablePosition::MACRO_METHOD])
        );
    }

    public function test_it_should_add_sortable_to_the_given_field()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'position' => 'position',
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
     * @return SortablePosition
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
        return Fluent::EXTENSION_NAME;
    }
}