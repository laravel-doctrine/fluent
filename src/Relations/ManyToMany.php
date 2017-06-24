<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\ManyToManyAssociationBuilder;
use Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Builders\Traits\QueuesMacros;
use LaravelDoctrine\Fluent\Extensions\Gedmo\GedmoManyToManyHints;
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
    use ManyTo, Owning, Ownable, Orderable, Indexable, Macroable, QueuesMacros;
    use GedmoManyToManyHints;

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
     * @param bool   $unique
     *
     * @return $this
     */
    public function joinColumn($joinColumn, $references = 'id', $unique = false)
    {
        $this->addJoinColumn(null, $joinColumn, $references, false, $unique);

        return $this;
    }

    /**
     * @param string $foreignKey
     * @param string $references
     * @param bool   $unique
     *
     * @return $this
     */
    public function foreignKey($foreignKey, $references = 'id', $unique = false)
    {
        return $this->joinColumn($foreignKey, $references, $unique);
    }

    /**
     * @param string $foreignKey
     * @param string $references
     * @param bool   $unique
     *
     * @return $this
     */
    public function source($foreignKey, $references = 'id', $unique = false)
    {
        return $this->joinColumn($foreignKey, $references, $unique);
    }

    /**
     * @param string $inverseKey
     * @param string $references
     * @param bool   $unique
     *
     * @return $this
     */
    public function inverseKey($inverseKey, $references = 'id', $unique = false)
    {
        $this->association->addInverseJoinColumn($inverseKey, $references, false, $unique);

        return $this;
    }

    /**
     * @param string $inverseKey
     * @param string $references
     * @param bool   $unique
     *
     * @return $this
     */
    public function target($inverseKey, $references = 'id', $unique = false)
    {
        return $this->inverseKey($inverseKey, $references, $unique);
    }

    /**
     * Magic call method works as a proxy for the Doctrine associationBuilder.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        if ($this->hasMacro($method)) {
            return $this->queueMacro($method, $args);
        }

        return parent::__call($method, $args);
    }
}
