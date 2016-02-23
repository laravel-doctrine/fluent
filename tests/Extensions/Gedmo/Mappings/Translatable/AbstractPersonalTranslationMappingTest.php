<?php

namespace Tests\Extensions\Gedmo\Mappings\Translatable;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Translatable\AbstractPersonalTranslationMapping;
use Tests\Extensions\Gedmo\Mappings\MappingTestCase;

class AbstractPersonalTranslationMappingTest extends MappingTestCase
{
    protected function getMappingClass()
    {
        return AbstractPersonalTranslationMapping::class;
    }

    protected function getMappedClass()
    {
        return AbstractPersonalTranslation::class;
    }

    protected function configureMocks()
    {
        $this->builder->shouldReceive('integer')->with('id')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('locale')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('field')->once()->andReturn($this->field);
        $this->builder->shouldReceive('text')->with('content')->once()->andReturn($this->field);
        
        $this->field->shouldReceive('unsigned')->once()->andReturnSelf();
        $this->field->shouldReceive('primary')->once()->andReturnSelf();
        $this->field->shouldReceive('generatedValue')->with(\Mockery::type(\Closure::class))->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(8)->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(32)->once()->andReturnSelf();
        $this->field->shouldReceive('nullable')->once()->andReturnSelf();
    }
}
