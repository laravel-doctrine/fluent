<?php

namespace LaravelDoctrine\Fluent\Mappers;

final class EntityMapper extends AbstractMapper
{
    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @return bool
     */
    public function isTransient()
    {
        return false;
    }
}
