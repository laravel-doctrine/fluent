<?php

namespace Tests\Stubs\Mappings;

use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;
use Tests\Stubs\Entities\StubEntity;

class StubEntityMapping implements Mapping
{
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('name');
    }

    public function mapFor()
    {
        return StubEntity::class;
    }
}
