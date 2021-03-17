<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeSelfReference;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
class TreeSelfReferenceTest extends TestCase
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    protected function setUp(): void
    {
        $this->fieldName     = 'root';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();
    }

    /**
     * @dataProvider getMethods
     */
    public function test_it_should_add_itself_as_a_field_macro($method)
    {
        TreeSelfReference::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreeSelfReference::class,
            call_user_func([$field, $method])
        );
    }

    /**
     * @dataProvider getMethods
     */
    public function test_it_should_add_itself_as_a_many_to_one_macro($method)
    {
        TreeSelfReference::enable();

        $relation = new ManyToOne(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), new DefaultNamingStrategy(), $this->fieldName, 'Foo'
        );

        $this->assertInstanceOf(
            TreeSelfReference::class,
            call_user_func([$relation, $method])
        );
    }

    public function getMethods()
    {
        return [
            ['treeRoot'],
            ['treeParent'],
        ];
    }

    public function test_the_root_macro_sets_the_field_as_nullable()
    {
        TreeSelfReference::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $field = Field::make(new ClassMetadataBuilder($classMetadata), 'integer', $this->fieldName);
        $field->treeRoot();
        $field->build();

        $this->assertTrue($classMetadata->getFieldMapping($this->fieldName)['nullable']);
    }

    public function test_the_root_macro_sets_the_relation_as_nullable()
    {
        TreeSelfReference::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $relation = new ManyToOne(new ClassMetadataBuilder($classMetadata), new DefaultNamingStrategy(), $this->fieldName, 'Foo');
        $relation->treeRoot();
        $relation->build();

        $this->assertTrue($relation->getJoinColumn()->isNullable());
    }

    public function test_the_parent_macro_sets_the_field_as_nullable()
    {
        TreeSelfReference::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $field = Field::make(new ClassMetadataBuilder($classMetadata), 'integer', $this->fieldName);
        $field->treeParent();
        $field->build();

        $this->assertTrue($classMetadata->getFieldMapping($this->fieldName)['nullable']);
    }

    public function test_the_parent_macro_sets_the_relation_as_nullable()
    {
        TreeSelfReference::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $relation = new ManyToOne(new ClassMetadataBuilder($classMetadata), new DefaultNamingStrategy(), $this->fieldName, 'Foo');
        $relation->treeParent();
        $relation->build();

        $this->assertTrue($relation->getJoinColumn()->isNullable());
    }

    /**
     * @dataProvider getKeys
     */
    public function test_can_mark_a_field_as($key)
    {
        $this->getExtension($key)->build();

        $this->assertBuildResultIs([
            $key => $key,
        ]);
    }

    public function getKeys()
    {
        return [
            ['root'],
            ['parent'],
        ];
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
     * @param string $key
     *
     * @return TreeSelfReference
     */
    protected function getExtension($key)
    {
        return new TreeSelfReference($this->classMetadata, $key, $key);
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TreeDriver::EXTENSION_NAME;
    }
}
