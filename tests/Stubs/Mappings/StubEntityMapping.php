<?php

namespace Tests\Stubs\Mappings;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Tests\Stubs\Entities\StubEntity;

class StubEntityMapping extends EntityMapping
{
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('name');
        $builder->belongsTo('parent', StubEntity::class)->inversedBy('parent');
        $builder->hasMany('children', StubEntity::class)->mappedBy('parent');
    }

    public function mapFor()
    {
        return StubEntity::class;
    }
}
