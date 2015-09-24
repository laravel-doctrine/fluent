<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Relations\JoinColumn;

trait ManyTo
{
    /**
     * @var JoinColumn[]
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
            $this->getNamingStrategy(),
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
     * @return JoinColumn[]
     */
    public function getJoinColumns()
    {
        return $this->joinColumns;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();

    /**
     * @return NamingStrategy
     */
    abstract public function getNamingStrategy();
}
