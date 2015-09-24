<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\ManyToManyAssociationBuilder;
use Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder;
use LaravelDoctrine\Fluent\Relations\Traits\Indexable;
use LaravelDoctrine\Fluent\Relations\Traits\ManyTo;
use LaravelDoctrine\Fluent\Relations\Traits\Orderable;
use LaravelDoctrine\Fluent\Relations\Traits\Ownable;
use LaravelDoctrine\Fluent\Relations\Traits\Owning;

/**
 * @method $this mappedBy($fieldName)
 * @method $this inversedBy($fieldName)
 * @method $this orphanRemoval()
 */
class ManyToMany extends AbstractRelation
{
    use ManyTo, Owning, Ownable, Orderable, Indexable;

    /**
     * @var ManyToManyAssociationBuilder
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
        return $this->builder->createManyToMany($relation, $entity);
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function joinTable($table)
    {
        $this->association->setJoinTable($table);

        return $this;
    }

    /**
     * @param string $joinColumn
     * @param string $references
     *
     * @return $this
     */
    public function joinColumn($joinColumn, $references = 'id')
    {
        $this->addJoinColumn(null, $joinColumn, $references, false);

        return $this;
    }

    /**
     * @param string $foreignKey
     * @param string $references
     *
     * @return $this
     */
    public function foreignKey($foreignKey, $references = 'id')
    {
        return $this->joinColumn($foreignKey, $references);
    }

    /**
     * @param string $inverseKey
     * @param string $references
     *
     * @return $this
     */
    public function inverseKey($inverseKey, $references = 'id')
    {
        $this->association->addInverseJoinColumn($inverseKey, $references, false);

        return $this;
    }
}
