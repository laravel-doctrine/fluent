<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use LaravelDoctrine\Fluent\Relations\JoinColumn;

trait ManyTo
{
    /**
     * @var JoinColumn[]
     */
    protected $joinColumns = [];

    /**
     * Build the association.
     */
    public function build()
    {
        foreach ($this->getJoinColumns() as $column) {
            $this->getAssociation()->addJoinColumn(
                $column->getJoinColumn(),
                $column->getReferenceColumn(),
                $column->isNullable(),
                $column->isUnique(),
                $column->getOnDelete(),
                $column->getColumnDefinition()
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
     * @param string|null $columnDefinition
     *
     * @return $this
     */
    public function addJoinColumn(
        $relation,
        $joinColumn = null,
        $referenceColumn = null,
        $nullable = false,
        $unique = false,
        $onDelete = null,
        $columnDefinition = null
    ) {
        $joinColumn = new JoinColumn(
            $this->getNamingStrategy(),
            $relation,
            $joinColumn,
            $referenceColumn,
            $nullable,
            $unique,
            $onDelete,
            $columnDefinition
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
     * @return \Doctrine\ORM\Mapping\Builder\AssociationBuilder
     */
    abstract public function getAssociation();

    /**
     * @return \Doctrine\ORM\Mapping\NamingStrategy
     */
    abstract public function getNamingStrategy();
}
