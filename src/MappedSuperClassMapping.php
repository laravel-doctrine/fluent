<?php

namespace LaravelDoctrine\Fluent;

use LaravelDoctrine\Fluent\Mappers\MappedSuperClassMapper;
use LaravelDoctrine\Fluent\Mappers\MapperSet;

abstract class MappedSuperClassMapping implements Mapping
{
    /**
     * {@inheritdoc}
     */
    public function addMapperTo(MapperSet $mappers)
    {
        $mappers->addMapper($this->mapFor(), new MappedSuperClassMapper($this));
    }
}
