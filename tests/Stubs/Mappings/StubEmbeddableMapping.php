<?php

namespace Tests\Stubs\Mappings;

use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mapping;
use Tests\Stubs\Embedabbles\StubEmbeddable;

class StubEmbeddableMapping implements Mapping
{
    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
        $builder->string('name');
    }

    /**
     * Returns the fully qualified name of the entity that this mapper maps.
     * @return string
     */
    public function mapFor()
    {
        return StubEmbeddable::class;
    }
}
