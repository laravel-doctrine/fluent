<?php

namespace LaravelDoctrine\Fluent;

abstract class EmbeddableMapping implements Mapping
{
    /**
     * The given class should be mapped as Entity, Embeddable or MappedSuperClass
     *
     * @return string
     */
    public function mapAs()
    {
        return Mapping::EMBEDDABLE;
    }
}
