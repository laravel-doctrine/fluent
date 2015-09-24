<?php

namespace LaravelDoctrine\Fluent\Relations;

use BadMethodCallException;
use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use InvalidArgumentException;

/**
 * @method $this orphanRemoval()
 * @method $this makePrimaryKey()
 * @method $this cascadeAll()
 * @method $this cascadePersist()
 * @method $this cascadeRemove()
 * @method $this cascadeMerge()
 * @method $this cascadeDetach()
 * @method $this cascadeRefresh()
 * @method $this fetchExtraLazy()
 * @method $this fetchEager()
 * @method $this fetchLazy()
 */
abstract class AbstractRelation implements Relation
{
    /**
     * @var string
     */
    protected $relation;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var AssociationBuilder
     */
    protected $association;

    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param string               $relation
     * @param string               $entity
     */
    public function __construct(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy, $relation, $entity)
    {
        $this->entity         = $entity;
        $this->builder        = $builder;
        $this->relation       = $relation;
        $this->namingStrategy = $namingStrategy;
        $this->association    = $this->createAssociation($builder, $relation, $entity);
    }

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return AssociationBuilder
     */
    abstract protected function createAssociation(ClassMetadataBuilder $builder, $relation, $entity);

    /**
     * @param string[] $cascade
     * @Enum({"persist", "remove", "merge", "detach", "refresh", "ALL"})
     *
     * @return $this
     */
    public function cascade(array $cascade)
    {
        foreach ($cascade as $name) {
            $method = 'cascade' . studly_case(strtolower($name));

            if (!method_exists($this->association, $method)) {
                throw new InvalidArgumentException('Cascade [' . $name . '] does not exist');
            }

            $this->{$method}();
        }

        return $this;
    }

    /**
     * @param string $strategy
     * @Enum({"LAZY", "EAGER", "EXTRA_LAZY"})
     *
     * @return $this
     */
    public function fetch($strategy)
    {
        $method = 'fetch' . studly_case(strtolower($strategy));

        if (!method_exists($this->association, $method)) {
            throw new InvalidArgumentException('Fetch [' . $strategy . '] does not exist');
        }

        $this->{$method}();

        return $this;
    }

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Build the association
     */
    public function build()
    {
        $this->association->build();
    }

    /**
     * @return AssociationBuilder
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Magic call method works as a proxy for the Doctrine associationBuilder
     *
     * @param string $method
     * @param array  $args
     *
     * @throws BadMethodCallException
     * @return $this
     */
    public function __call($method, $args)
    {
        if (method_exists($this->getAssociation(), $method)) {
            call_user_func_array([$this->getAssociation(), $method], $args);

            return $this;
        }

        throw new BadMethodCallException("Relation method [{$method}] does not exist.");
    }

    /**
     * @return NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }
}
