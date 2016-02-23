<?php

namespace Tests\Extensions\Gedmo\Mappings\Tree;

use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Tree\AbstractClosureMapping;
use Tests\Extensions\Gedmo\Mappings\MappingTestCase;

class AbstractClosureMappingTest extends MappingTestCase
{
    public function configureMocks()
    {
        $this->builder->shouldReceive('integer')->with('id')->once()->andReturn($this->field);
        $this->builder->shouldReceive('integer')->with('depth')->once()->andReturn(\Mockery::mock(Field::class));

        $this->field->shouldReceive('unsigned')->once()->andReturn($this->field);
        $this->field->shouldReceive('primary')->once()->andReturn($this->field);
        $this->field->shouldReceive('generatedValue')->once()->with(\Mockery::type(\Closure::class))->andReturn($this->field);
    }

    protected function getMappingClass()
    {
        return AbstractClosureMapping::class;
    }

    protected function getMappedClass()
    {
        return AbstractClosure::class;
    }
}
