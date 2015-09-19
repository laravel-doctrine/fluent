<?php

namespace LaravelDoctrine\Fluent;

interface Mapping
{
    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder);

    /**
     * Returns the fully qualified name of the entity that this mapper maps.
     *
     * @return string
     */
    public function mapFor();
}
