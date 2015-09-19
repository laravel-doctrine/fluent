<?php

namespace Tests\Stubs\Mappings2;

use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;
use Tests\Stubs\Entities\StubEntity3;

class StubEntity3Mapping implements Mapping
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
