<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\NestedSet;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Tree;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class NestedSetTest extends \PHPUnit_Framework_TestCase
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
     * @var NestedSet
     */
    private $extension;

    /**
     * @var Builder
     */
    private $builder;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->builder       = new Builder(new ClassMetadataBuilder($this->classMetadata));
        $this->extension     = new NestedSet($this->builder);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_building_a_nested_tree_through_the_tree_facade()
    {
        Tree::enable();

        $this->builder->tree()->asNestedSet()->left('izq')->right('der')->parent('padre');
        $this->builder->build();

        $this->assertExtensionEquals([
            'strategy'         => 'nested',
            'left'             => 'izq',
            'right'            => 'der',
            'level'            => null,
            'root'             => null,
            'parent'           => 'padre',
        ]);
    }

    public function test_can_mark_entity_as_a_nested_set_tree()
    {
        $this->extension->build();

        $this->assertExtensionKeyEquals('strategy', 'nested');
    }

    public function test_it_has_defaults_for_all_required_fields()
    {
        $this->extension->build();

        $this->assertExtensionEquals([
            'strategy'         => 'nested',
            'left'             => 'left',
            'right'            => 'right',
            'level'            => null,
            'root'             => null,
            'parent'           => 'parent',
        ]);
    }

    public function test_it_auto_completes_missing_required_fields()
    {
        $this->extension->left('lft')->right('rgt')->build();

        $this->assertExtensionEquals([
            'strategy'         => 'nested',
            'left'             => 'lft',
            'right'            => 'rgt',
            'level'            => null,
            'root'             => null,
            'parent'           => 'parent',
        ]);
    }

    /**
     * @param string $fieldName
     * @dataProvider getAllFields
     */
    public function test_can_set_a_custom_field($fieldName)
    {
        $this->extension->$fieldName('custom')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_integer($fieldName)
    {
        $this->extension->$fieldName('custom', 'integer')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_big_int($fieldName)
    {
        $this->extension->$fieldName('custom', 'bigint')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_small_int($fieldName)
    {
        $this->extension->$fieldName('custom', 'smallint')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @dataProvider getNumericFields
     */
    public function test_it_should_not_allow_strings_in_any_numeric_field($fieldName)
    {
        $this->setExpectedException(InvalidMappingException::class);

        $this->extension->$fieldName('custom', 'string')->build();
    }

    public function test_it_should_create_a_belongs_to_relation_to_the_parent_class_on_the_given_field()
    {
    	$this->extension->parent('myself')->build();
        $this->builder->build();

        $this->assertExtensionKeyEquals('parent', 'myself');
        $this->assertArrayHasKey('myself', $this->classMetadata->associationMappings);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $this->classMetadata->associationMappings['myself']['type']);
    }

    public function test_it_should_create_a_belongs_to_relation_to_the_root_class_on_the_given_field()
    {
    	$this->extension->root('granpa')->build();
        $this->builder->build();

        $this->assertExtensionKeyEquals('root', 'granpa');
        $this->assertArrayHasKey('granpa', $this->classMetadata->associationMappings);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $this->classMetadata->associationMappings['granpa']['type']);
    }

    /**
     * @dataProvider getNumericFields
     * @param string $fieldName
     */
    public function test_it_allows_further_field_configuration_through_a_callback($fieldName)
    {
    	$mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once();

        $this->extension->$fieldName('custom', 'integer', function($field) use ($mock) {
            $this->assertInstanceOf(Field::class, $field);
            $mock->callMe();
        });
    }

    /**
     * @dataProvider getRelationFields
     */
    public function test_it_allows_relation_configuration_through_a_callback($relation)
    {
        $mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once();

        $this->extension->$relation('myself', function($belongsTo) use ($mock) {
            $this->assertInstanceOf(ManyToOne::class, $belongsTo);
            $mock->callMe();
        });
    }

    public function test_it_sets_up_gedmos_repository()
    {
    	$this->extension->build();

        $this->assertEquals(NestedTreeRepository::class, $this->builder->getClassMetadata()->customRepositoryClassName);
    }

    public function getNumericFields()
    {
        return [
            ['left'],
            ['right'],
            ['level'],
        ];
    }

    public function getRelationFields()
    {
        return [['root'], ['parent']];
    }

    public function getAllFields()
    {
        return array_merge($this->getNumericFields(), $this->getRelationFields());
    }

    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertExtensionEquals(array $expected)
    {
        $this->assertEquals($expected, $this->getExtension());
    }

    /**
     * Assert that a given key of the built extension matches the expected value.
     *
     * @param string $key
     * @param mixed $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertExtensionKeyEquals($key, $expected)
    {
        $extension = $this->getExtension();

        $this->assertArrayHasKey($key, $extension, "Extension does not have key [$key].");
        $this->assertEquals($expected, $extension[$key]);
    }

    /**
     * @return array
     */
    protected function getExtension()
    {
        return $this->classMetadata->getExtension(TreeDriver::EXTENSION_NAME);
    }
}
