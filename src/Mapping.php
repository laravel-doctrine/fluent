<?php

namespace LaravelDoctrine\Fluent;

use LaravelDoctrine\Fluent\Mappers\MapperSet;

interface Mapping
{
    /**
     * Returns the fully qualified name of the class that this mapper maps.
     *
     * @return string
     */
    public function mapFor();

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder);

    /**
     * Create the corresponding mapper and add it to the current mapper set.
     *
     * @param MapperSet $mappers
     */
    public function addMapperTo(MapperSet $mappers);
}
