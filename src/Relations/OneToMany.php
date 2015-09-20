<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder;
use LaravelDoctrine\Fluent\Relations\Traits\Indexable;
use LaravelDoctrine\Fluent\Relations\Traits\Orderable;
use LaravelDoctrine\Fluent\Relations\Traits\Ownable;

/**
 * @method $this mappedBy($fieldName)
 */
class OneToMany extends AbstractRelation
{
    use Ownable, Orderable, Indexable;

    /**
     * @var OneToManyAssociationBuilder
     */
    protected $association;

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return OneToManyAssociationBuilder
     */
    protected function createAssociation(ClassMetadataBuilder $builder, $relation, $entity)
    {
        return $this->builder->createOneToMany($relation, $entity);
    }
}
