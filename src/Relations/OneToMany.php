<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder;

/**
 * @method $this mappedBy($fieldName)
 * @method $this setIndexBy($fieldName)
 * @method $this setOrderBy($order)
 */
class OneToMany extends AbstractRelation
{
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

    /**
     * @param        $name
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($name, $order = 'ASC')
    {
        $this->association->setOrderBy([
            $name => $order
        ]);

        return $this;
    }
}
