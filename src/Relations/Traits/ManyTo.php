<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use LaravelDoctrine\Fluent\Relations\JoinColumn;

trait ManyTo
{
    /**
     * @var array
     */
    protected $joinColumns = [];

    /**
     * Build the association
     */
    public function build()
    {
        foreach ($this->getJoinColumns() as $column) {
            $this->getAssociation()->addJoinColumn(
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
     * @param string      $relation
     * @param string|null $joinColumn
     * @param string|null $referenceColumn
     * @param bool|false  $nullable
     * @param bool|false  $unique
     * @param string|null $onDelete
     *
     * @return $this
     */
    public function addJoinColumn(
        $relation,
        $joinColumn = null,
        $referenceColumn = null,
        $nullable = false,
        $unique = false,
        $onDelete = null
    ) {
        $joinColumn = new JoinColumn(
            $this->namingStrategy,
            $relation,
            $joinColumn,
            $referenceColumn,
            $nullable,
            $unique,
            $onDelete
        );

        $this->pushJoinColumn($joinColumn);

        return $this;
    }

    /**
     * @param JoinColumn $column
     */
    protected function pushJoinColumn(JoinColumn $column)
    {
        $this->joinColumns[] = $column;
    }

    /**
     * @return array|JoinColumn[]
     */
    public function getJoinColumns()
    {
        return $this->joinColumns;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();
}
