<?php

namespace Tests\Extensions\Gedmo\Mappings\Translatable;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Translatable\AbstractTranslationMapping;
use Tests\Extensions\Gedmo\Mappings\MappingTestCase;

class AbstractTranslationMappingTest extends MappingTestCase
{
    protected function getMappingClass()
    {
        return AbstractTranslationMapping::class;
    }

    protected function getMappedClass()
    {
        return AbstractTranslation::class;
    }

    protected function configureMocks()
    {
        $this->builder->shouldReceive('integer')->with('id')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('locale')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('objectClass')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('field')->once()->andReturn($this->field);
        $this->builder->shouldReceive('string')->with('foreignKey')->once()->andReturn($this->field);
        $this->builder->shouldReceive('text')->with('content')->once()->andReturn($this->field);

        $this->field->shouldReceive('unsigned')->once()->andReturnSelf();
        $this->field->shouldReceive('primary')->once()->andReturnSelf();
        $this->field->shouldReceive('generatedValue')->with(
            $this->generatedValueExpectation()
        )->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(8)->once()->andReturnSelf();
        $this->field->shouldReceive('name')->with('object_class')->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(32)->once()->andReturnSelf();
        $this->field->shouldReceive('length')->with(64)->once()->andReturnSelf();
        $this->field->shouldReceive('name')->with('foreign_key')->once()->andReturnSelf();
        $this->field->shouldReceive('nullable')->once()->andReturnSelf();
    }
}
