<?php

namespace Tests\Stubs\Mappings2;

use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;
use Tests\Stubs\Entities\StubEntity2;

class StubEntity2Mapping implements Mapping
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
