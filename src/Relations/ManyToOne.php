<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Builders\Traits\QueuesMacros;
use LaravelDoctrine\Fluent\Extensions\Gedmo\GedmoManyToOneHints;
use LaravelDoctrine\Fluent\Relations\Traits\ManyTo;
use LaravelDoctrine\Fluent\Relations\Traits\Owning;
use LaravelDoctrine\Fluent\Relations\Traits\Primary;

/**
 * @method $this inversedBy($fieldName)
 * @method $this foreignKey($foreignKey)
 * @method $this localKey($localKey)
 * @method $this target($foreignKey)
 * @method $this source($localKey)
 * @method $this setJoinColumn($joinColumn)
 * @method $this setReferenceColumn($referenceColumn)
 * @method $this nullable()
 * @method $this unique()
 * @method $this onDelete($onDelete = null)
 */
class ManyToOne extends AbstractRelation
{
    use ManyTo, Owning, Primary, Macroable, QueuesMacros;
    use GedmoManyToOneHints;

    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param string               $relation
     * @param string               $entity
     */
    public function __construct(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy, $relation, $entity)
    {
        parent::__construct($builder, $namingStrategy, $relation, $entity);

        $this->addJoinColumn($relation);
    }

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return AssociationBuilder
     */
    protected function createAssociation(ClassMetadataBuilder $builder, $relation, $entity)
    {
        return $this->builder->createManyToOne(
            $relation,
            $entity
        );
    }

    /**
     * @param callable|null $callback
     *
     * @return JoinColumn|false
     */
    public function getJoinColumn(callable $callback = null)
    {
        $joinColumn = reset($this->joinColumns);

        if (is_callable($callback)) {
            $callback($joinColumn);
        }

        return $joinColumn;
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

        if (method_exists($this->getJoinColumn(), $method)) {
            call_user_func_array([$this->getJoinColumn(), $method], $args);

            return $this;
        }

        return parent::__call($method, $args);
    }
}
