<?php

namespace Tests\Extensions\Gedmo\Mappings;

use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;

abstract class MappingTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mapping
     */
    protected $mapping;

    /**
     * @var Fluent|\Mockery\Mock
     */
    protected $builder;

    /**
     * @var Field|\Mockery\Mock
     */
    protected $field;

    /**
     * Get the class name of the mapping to be tested.
     *
     * @return string
     */
    abstract protected function getMappingClass();

    /**
     * Get the class name of the mapped class.
     * @return string
     */
    abstract protected function getMappedClass();

    /**
     * Configure the Builder and the Field mocks.
     * This will be ran only for the `test_mapping` test method.
     *
     * @return void
     */
    abstract protected function configureMocks();

    protected function setUp()
    {
        $class = $this->getMappingClass();
        $this->mapping = new $class;

        $this->builder = \Mockery::mock(Fluent::class);
        $this->field = \Mockery::mock(Field::class);
    }

    public function test_mapping()
    {
        $this->configureMocks();

        $this->mapping->map($this->builder);
    }

    public function test_it_maps_the_mapped_class()
    {
        $this->assertEquals($this->getMappedClass(), $this->mapping->mapFor());
    }

    /**
     * Mockery argument validation for GeneratedValue objects.
     *
     * @param string $strategy
     *
     * @return \Mockery\Matcher\Closure
     */
    protected function generatedValueExpectation($strategy = 'identity')
    {
        return \Mockery::on(function($argument) use ($strategy) {
            /** @var GeneratedValue|\Mockery\Mock $gen */
            $gen = \Mockery::mock(GeneratedValue::class);
            $gen->shouldReceive($strategy)->once();

            if (!is_callable($argument)) {
                return false;
            }

            $argument($gen);
            return true;
        });
    }
}
