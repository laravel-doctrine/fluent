<?php

namespace Tests\Extensions\Gedmo\Sortable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Sortable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\SortableGroup;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class SortableGroupTest extends \PHPUnit_Framework_TestCase
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
     * @var SortableGroup
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'category';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new SortableGroup($this->classMetadata, $this->fieldName, 'name');
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        SortableGroup::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')
        ), 'string', $this->fieldName)->build();

        $this->assertInstanceOf(
            SortableGroup::class,
            call_user_func([$field, SortableGroup::MACRO_METHOD])
        );
    }

    public function test_it_should_add_itself_as_many_to_one_macro()
    {
        SortableGroup::enable();

        $manyToOne = new ManyToOne(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            $this->fieldName,
            'Bar'
        );

        $this->assertInstanceOf(
            SortableGroup::class,
            call_user_func([$manyToOne, SortableGroup::MACRO_METHOD])
        );
    }

    public function test_it_should_add_itself_as_many_to_many_macro()
    {
        SortableGroup::enable();

        $manyToMany = new ManyToMany(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            $this->fieldName,
            'Bar'
        );

        $this->assertInstanceOf(
            SortableGroup::class,
            call_user_func([$manyToMany, SortableGroup::MACRO_METHOD])
        );
    }

    public function test_it_should_add_sortable_to_the_given_group()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'groups' => ['category'],
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
     * @return SortableGroup
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