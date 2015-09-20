<?php

namespace Tests\Stubs\Mappings2;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Tests\Stubs\Entities\StubEntity2;

class StubEntity2Mapping extends EntityMapping
{
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('name');
    }

    public function mapFor()
    {
        return StubEntity2::class;
    }
}
