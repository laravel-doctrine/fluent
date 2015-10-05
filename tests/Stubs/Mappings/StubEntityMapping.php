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
        $builder->belongsTo(StubEntity::class, 'parent')->inversedBy('parent');
        $builder->hasMany(StubEntity::class, 'children')->mappedBy('parent');
        $builder->hasOne(StubEntity::class, 'one')->ownedBy('theOther');
        $builder->belongsToMany(StubEntity::class, 'many')->owns('theWorld');
    }

    public function mapFor()
    {
        return StubEntity::class;
    }
}
