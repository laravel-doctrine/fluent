<?php

namespace LaravelDoctrine\Fluent\Builders\Overrides;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use InvalidArgumentException;

class OverrideBuilderFactory
{
    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param                      $name
     * @param callable             $callback
     *
     * @return
     */
    public static function create(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy, $name, callable $callback)
    {
        foreach (self::getFactories() as $buildable => $check) {
            if ($check($builder->getClassMetadata(), $name)) {
                return new $buildable($builder, $namingStrategy, $name, $callback);
            }
        }

        throw new InvalidArgumentException('No attribute or association could be found for '.$name);
    }

    /**
     * @return array
     */
    protected static function getFactories()
    {
        return [
            AttributeOverride::class   => function (ClassMetadata $meta, $name) {
                return $meta->hasField($name);
            },
            AssociationOverride::class => function (ClassMetadata $meta, $name) {
                return $meta->hasAssociation($name);
            },
        ];
    }
}
