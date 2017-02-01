<?php

namespace Tests\Extensions\Gedmo\Mappings\Loggable;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Loggable\AbstractLogEntryMapping;
use Tests\Extensions\Gedmo\Mappings\MappingTestCase;

class AbstractLogEntryMappingTest extends MappingTestCase
{
    public function configureMocks()
    {
        $this->builder->shouldReceive('increments')->once()->with('id')->andReturn($this->field);
        $this->builder->shouldReceive('string')->once()->with('action')->andReturn($this->field);
        $this->builder->shouldReceive('dateTime')->once()->with('loggedAt')->andReturn($this->field);
        $this->builder->shouldReceive('string')->once()->with('objectId')->andReturn($this->field);
        $this->builder->shouldReceive('string')->once()->with('objectClass')->andReturn($this->field);
        $this->builder->shouldReceive('integer')->once()->with('version')->andReturn($this->field);
        $this->builder->shouldReceive('array')->once()->with('data')->andReturn($this->field);
        $this->builder->shouldReceive('string')->once()->with('username')->andReturn($this->field);

        $this->field->shouldReceive('length')->with(8)->once()->andReturnSelf();
        $this->field->shouldReceive('name')->with('logged_at')->once()->andReturnSelf();
        $this->field->shouldReceive('name')->with('object_id')->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(64)->once()->andReturnSelf();
        $this->field->shouldReceive('name')->with('object_class')->once()->andReturnSelf();
        $this->field->shouldReceive('nullable')->times(3)->andReturnSelf();
    }

    protected function getMappingClass()
    {
        return AbstractLogEntryMapping::class;
    }

    protected function getMappedClass()
    {
        return AbstractLogEntry::class;
    }
}
