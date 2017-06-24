<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\NamingStrategy;

class JoinColumn
{
    /**
     * @var string
     */
    protected $relation;

    /**
     * @var string
     */
    protected $joinColumn;

    /**
     * @var string
     */
    protected $referenceColumn;

    /**
     * @var bool
     */
    protected $nullable = true;

    /**
     * @var bool
     */
    protected $unique = false;

    /**
     * @var string|null
     */
    protected $onDelete = null;

    /**
     * @var string|null
     */
    protected $columnDefinition = null;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @param NamingStrategy $namingStrategy
     * @param string         $relation
     * @param string|null    $joinColumn
     * @param string|null    $referenceColumn
     * @param bool           $nullable
     * @param bool           $unique
     * @param string|null    $onDelete
     * @param string|null    $columnDefinition
     */
    public function __construct(
        NamingStrategy $namingStrategy,
        $relation,
        $joinColumn = null,
        $referenceColumn = null,
        $nullable = true,
        $unique = false,
        $onDelete = null,
        $columnDefinition = null
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->relation = $relation;
        $this->joinColumn = $joinColumn;
        $this->referenceColumn = $referenceColumn;
        $this->nullable = $nullable;
        $this->unique = $unique;
        $this->onDelete = $onDelete;
        $this->columnDefinition = $columnDefinition;
    }

    /**
     * @param string $foreignKey
     *
     * @return $this
     */
    public function foreignKey($foreignKey)
    {
        $this->joinColumn = $foreignKey;

        return $this;
    }

    /**
     * @param string $foreignKey
     *
     * @return $this
     */
    public function target($foreignKey)
    {
        return $this->foreignKey($foreignKey);
    }

    /**
     * @param string $localKey
     *
     * @return $this
     */
    public function localKey($localKey)
    {
        $this->referenceColumn = $localKey;

        return $this;
    }

    /**
     * @param string $localKey
     *
     * @return $this
     */
    public function source($localKey)
    {
        return $this->localKey($localKey);
    }

    /**
     * @return string
     */
    public function getJoinColumn()
    {
        return $this->joinColumn ?: $this->namingStrategy->joinColumnName($this->relation);
    }

    /**
     * @param string $joinColumn
     *
     * @return $this
     */
    public function setJoinColumn($joinColumn)
    {
        $this->joinColumn = $joinColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceColumn()
    {
        return $this->referenceColumn ?: $this->namingStrategy->referenceColumnName();
    }

    /**
     * @param string $referenceColumn
     *
     * @return $this
     */
    public function setReferenceColumn($referenceColumn)
    {
        $this->referenceColumn = $referenceColumn;

        return $this;
    }

    /**
     * @return $this
     */
    public function nullable()
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @return $this
     */
    public function unique()
    {
        $this->unique = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * @param string|null $onDelete
     *
     * @return $this
     */
    public function onDelete($onDelete = null)
    {
        $this->onDelete = $onDelete;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColumnDefinition()
    {
        return $this->columnDefinition;
    }
}
