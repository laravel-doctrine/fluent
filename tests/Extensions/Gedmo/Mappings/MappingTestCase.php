<?php

namespace Tests\Extensions\Gedmo\Mappings;

use LaravelDoctrine\Fluent\Builders\Field;
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
    
    abstract protected function getMappingClass();
    
    abstract protected function getMappedClass();
    
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
    
}
