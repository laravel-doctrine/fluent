<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeRoot;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class TreeRootTest extends \PHPUnit_Framework_TestCase
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
     * @var TreeRoot
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'root';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();

        $this->extension = new TreeRoot($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        TreeRoot::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreeRoot::class,
            call_user_func([$field, TreeRoot::MACRO_METHOD])
        );
    }

    public function test_it_should_add_itself_as_a_many_to_one_macro()
    {
        TreeRoot::enable();

        $relation = new ManyToOne(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), new DefaultNamingStrategy(), $this->fieldName, 'Foo'
        );

        $this->assertInstanceOf(
            TreeRoot::class,
            call_user_func([$relation, TreeRoot::MACRO_METHOD])
        );
    }

    public function test_the_macro_sets_the_field_as_nullable()
    {
        TreeRoot::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $field = Field::make(new ClassMetadataBuilder($classMetadata), 'integer', $this->fieldName);
        $field->treeRoot();
        $field->build();

        $this->assertTrue($classMetadata->getFieldMapping($this->fieldName)['nullable']);
    }

    public function test_the_macro_sets_the_relation_as_nullable()
    {
        TreeRoot::enable();

        $classMetadata = new ExtensibleClassMetadata('Foo');
        $relation = new ManyToOne(new ClassMetadataBuilder($classMetadata), new DefaultNamingStrategy(), $this->fieldName, 'Foo');
        $relation->treeRoot();
        $relation->build();

        $this->assertTrue($relation->getJoinColumn()->isNullable());
    }

    public function test_can_mark_a_field_as_root()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'root' => 'root',
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
     * @return TreeRoot
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
