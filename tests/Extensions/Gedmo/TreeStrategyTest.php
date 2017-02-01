<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeStrategy;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

abstract class TreeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fluent
     */
    protected $builder;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var TreeStrategy
     */
    protected $strategy;

    /**
     * @param Fluent $builder
     * @return TreeStrategy
     */
    abstract protected function getStrategy(Fluent $builder);

    /**
     * @before
     */
    protected function bootStrategy()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->builder       = new Builder(new ClassMetadataBuilder($this->classMetadata));
        $this->strategy      = $this->getStrategy($this->builder);
    }

    public function test_it_should_create_a_belongs_to_relation_to_the_parent_class_on_the_given_field()
    {
        $this->strategy->parent('myself')->build();
        $this->builder->build();

        $this->assertExtensionKeyEquals('parent', 'myself');
        $this->assertArrayHasKey('myself', $this->classMetadata->associationMappings);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $this->classMetadata->associationMappings['myself']['type']);
    }

    /**
     * @param string $fieldName
     * @dataProvider getAllFields
     */
    public function test_can_set_a_custom_field($fieldName)
    {
        $this->strategy->$fieldName('custom')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_integer($fieldName)
    {
        $this->strategy->$fieldName('custom', 'integer')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_big_int($fieldName)
    {
        $this->strategy->$fieldName('custom', 'bigint')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @param string $fieldName
     * @dataProvider getNumericFields
     */
    public function test_can_set_a_custom_field_as_small_int($fieldName)
    {
        $this->strategy->$fieldName('custom', 'smallint')->build();

        $this->assertExtensionKeyEquals($fieldName, 'custom');
    }

    /**
     * @dataProvider getNumericFields
     */
    public function test_it_should_not_allow_strings_in_any_numeric_field($fieldName)
    {
        $this->setExpectedException(InvalidMappingException::class);

        $this->strategy->$fieldName('custom', 'string')->build();
    }

    /**
     * @dataProvider getNumericFields
     * @param string $fieldName
     */
    public function test_it_allows_further_field_configuration_through_a_callback($fieldName)
    {
        $mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once();

        $this->strategy->$fieldName('custom', 'integer', function($field) use ($mock) {
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

        $this->strategy->$relation('myself', function($belongsTo) use ($mock) {
            $this->assertInstanceOf(ManyToOne::class, $belongsTo);
            $mock->callMe();
        });
    }

    public function test_it_always_maps_the_parent_self_reference_relation()
    {
    	$this->strategy->build();

        $this->assertExtensionKeyEquals('parent', 'parent');
    }


    public function getNumericFields()
    {
        return [
            ['level'],
        ];
    }

    public function getRelationFields()
    {
        return [['parent']];
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
