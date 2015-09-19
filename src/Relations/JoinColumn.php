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
    protected $nullable = false;

    /**
     * @var bool
     */
    protected $unique = false;

    /**
     * @var null
     */
    protected $onDelete = null;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @param NamingStrategy $namingStrategy
     * @param string         $relation
     * @param string|null    $joinColumn
     * @param string|null    $referenceColumn
     * @param bool|false     $nullable
     * @param bool|false     $unique
     * @param null           $onDelete
     */
    public function __construct(
        NamingStrategy $namingStrategy,
        $relation,
        $joinColumn = null,
        $referenceColumn = null,
        $nullable = false,
        $unique = false,
        $onDelete = null
    ) {
        $this->unique          = $unique;
        $this->nullable        = $nullable;
        $this->onDelete        = $onDelete;
        $this->joinColumn      = $joinColumn;
        $this->referenceColumn = $referenceColumn;
        $this->relation        = $relation;
        $this->namingStrategy  = $namingStrategy;
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
     * @return null
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
}
