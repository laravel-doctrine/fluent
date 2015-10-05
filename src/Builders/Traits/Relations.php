<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;

trait Relations
{
    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function hasOne($entity, $field, callable $callback = null)
    {
        return $this->oneToOne($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function oneToOne($entity, $field, callable $callback = null)
    {
        return $this->addRelation(
            new OneToOne(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($entity, $field, callable $callback = null)
    {
        return $this->manyToOne($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($entity, $field, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToOne(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($entity, $field, callable $callback = null)
    {
        return $this->oneToMany($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function oneToMany($entity, $field, callable $callback = null)
    {
        return $this->addRelation(
            new OneToMany(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function belongsToMany($entity, $field, callable $callback = null)
    {
        return $this->manyToMany($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function manyToMany($entity, $field, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToMany(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * Adds a custom relation to the entity.
     *
     * @param Relation      $relation
     * @param callable|null $callback
     *
     * @return Relation
     */
    public function addRelation(Relation $relation, callable $callback = null)
    {
        $this->callbackAndQueue($relation, $callback);

        return $relation;
    }

    /**
     * @return \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder
     */
    abstract public function getBuilder();

    /**
     * @param Buildable     $buildable
     * @param callable|null $callback
     */
    abstract protected function callbackAndQueue(Buildable $buildable, callable $callback = null);

    /**
     * @return \Doctrine\ORM\Mapping\NamingStrategy
     */
    abstract public function getNamingStrategy();
}
