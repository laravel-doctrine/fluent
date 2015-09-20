<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Relations\Traits\Ownable;
use LaravelDoctrine\Fluent\Relations\Traits\Owning;

/**
 * @method $this inversedBy($fieldName)
 * @method $this mappedBy($fieldName)
 */
class OneToOne extends AbstractRelation
{
    use Owning, Ownable;

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return AssociationBuilder
     */
    protected function createAssociation(ClassMetadataBuilder $builder, $relation, $entity)
    {
        return $this->builder->createOneToOne(
            $relation,
            $entity
        );
    }
}
