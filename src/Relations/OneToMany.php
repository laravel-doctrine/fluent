<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @method $this mappedBy($fieldName)
 * @method $this setIndexBy($fieldName)
 * @method $this setOrderBy($order)
 */
class OneToMany extends AbstractRelation
{
    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return AssociationBuilder
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
