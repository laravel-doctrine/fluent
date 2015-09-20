<?php

namespace LaravelDoctrine\Fluent;

interface Mapping
{
    /**
     * Map the class as Entity
     *
     * @const
     */
    const ENTITY = 'Entity';

    /**
     * Map the class as Embeddable
     *
     * @const
     */
    const EMBEDDABLE = 'Embeddable';

    /**
     * Map the class as MappedSuperClass
     *
     * @const
     */
    const MAPPED_SUPER_CLASS = 'MappedSuperClass';

    /**
     * The given class should be mapped as Entity, Embeddable or MappedSuperClass
     *
     * @return string
     */
    public function mapAs();

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder);

    /**
     * Returns the fully qualified name of the class that this mapper maps.
     *
     * @return string
     */
    public function mapFor();
}
