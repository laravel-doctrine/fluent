<?php

namespace LaravelDoctrine\Fluent\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use InvalidArgumentException;

class InheritanceFactory
{
    /**
     * @var array
     */
    protected static $map = [
        'JOINED'            => JoinedTableInheritance::class,
        'SINGLE_TABLE'      => SingleTableInheritance::class,
        Inheritance::SINGLE => SingleTableInheritance::class,
        Inheritance::JOINED => JoinedTableInheritance::class,
    ];

    /**
     * @param string               $type
     * @param ClassMetadataBuilder $builder
     *
     * @return Inheritance
     */
    public static function create($type, ClassMetadataBuilder $builder)
    {
        if (isset(static::$map[$type])) {
            return new static::$map[$type]($builder);
        }

        throw new InvalidArgumentException("Inheritance type [{$type}] does not exist. SINGLE_TABLE and JOINED are support");
    }
}
