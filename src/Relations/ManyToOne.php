<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @method $this inversedBy($fieldName)
 */
class ManyToOne extends AbstractRelation
{
    /**
     * @var array
     */
    protected $joinColumns = [];

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $relation
     * @param string               $entity
     *
     * @return AssociationBuilder
     */
    protected function createAssociation(ClassMetadataBuilder $builder, $relation, $entity)
    {
        $this->addJoinColumn(new JoinColumn(
            $this->namingStrategy,
            $this->relation
        ));

        return $this->builder->createManyToOne(
            $relation,
            $entity
        );
    }

    /**
     * Build the association
     */
    public function build()
    {
        foreach ($this->getJoinColumns() as $column) {
            $this->association->addJoinColumn(
                $column->getJoinColumn(),
                $column->getReferenceColumn(),
                $column->isNullable(),
                $column->isUnique(),
                $column->getOnDelete()
            );
        }

        parent::build();
    }

    /**
     * @return $this
     */
    public function nullable()
    {
        $this->getJoinColumn()->nullable();

        return $this;
    }

    /**
     * @return $this
     */
    public function unique()
    {
        $this->getJoinColumn()->unique();

        return $this;
    }

    /**
     * @param null $onDelete
     *
     * @return $this
     */
    public function onDelete($onDelete = null)
    {
        $this->getJoinColumn()->onDelete($onDelete);

        return $this;
    }

    /**
     * @param JoinColumn $column
     */
    public function addJoinColumn(JoinColumn $column)
    {
        $this->joinColumns[] = $column;
    }

    /**
     * @return JoinColumn
     */
    public function getJoinColumn()
    {
        return reset($this->joinColumns);
    }

    /**
     * @return array|JoinColumn[]
     */
    public function getJoinColumns()
    {
        return $this->joinColumns;
    }
}
