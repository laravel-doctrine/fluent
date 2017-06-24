<?php

namespace LaravelDoctrine\Fluent\Builders\Inheritance;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

interface Inheritance
{
    /**
     * Set inheritance to single table mode.
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html#single-table-inheritance
     *       Doctine documentation
     */
    const SINGLE = ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE;

    /**
     * Set inheritance to joined table mode.
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html#class-table-inheritance
     *       Doctine documentation
     */
    const JOINED = ClassMetadataInfo::INHERITANCE_TYPE_JOINED;

    /**
     * Add the discriminator column.
     *
     * @param string $column
     * @param string $type
     * @param int    $length
     *
     * @return Inheritance
     */
    public function column($column, $type = 'string', $length = 255);

    /**
     * @param string      $name
     * @param string|null $class
     *
     * @return Inheritance
     */
    public function map($name, $class = null);
}
