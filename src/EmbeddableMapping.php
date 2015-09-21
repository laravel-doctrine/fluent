<?php

namespace LaravelDoctrine\Fluent;

use LaravelDoctrine\Fluent\Mappers\EmbeddableMapper;
use LaravelDoctrine\Fluent\Mappers\MapperSet;

abstract class EmbeddableMapping implements Mapping
{
    /**
     * {@inheritdoc}
     */
    public function addMapperTo(MapperSet $mappers)
    {
        $mappers->addMapper($this->mapFor(), new EmbeddableMapper($this));
    }
}
