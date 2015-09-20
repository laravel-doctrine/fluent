<?php

namespace Tests\Stubs\Mappings3;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Tests\Stubs\Entities\StubEntity3;

class StubEntity3Mapping extends EntityMapping
{
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('name');
    }

    public function mapFor()
    {
        return StubEntity3::class;
    }
}
