<?php

namespace LaravelDoctrine\Fluent;

abstract class EntityMapping implements Mapping
{
    /**
     * The given class should be mapped as Entity, Embeddable or MappedSuperClass
     *
     * @return string
     */
    public function mapAs()
    {
        return Mapping::ENTITY;
    }
}
