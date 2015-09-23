<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;

trait Relations
{
    /**
     * @param string        $field
     * @param string        $entity
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function hasOne($field, $entity, callable $callback = null)
    {
        return $this->oneToOne($field, $entity, $callback);
    }

    /**
     * @param string        $field
     * @param string        $entity
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function oneToOne($field, $entity, callable $callback = null)
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
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($field, $entity, callable $callback = null)
    {
        return $this->manyToOne($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($field, $entity, callable $callback = null)
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
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($field, $entity, callable $callback = null)
    {
        return $this->oneToMany($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return OneToMany
     */
    public function oneToMany($field, $entity, callable $callback = null)
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
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function belongsToMany($field, $entity, callable $callback = null)
    {
        return $this->manyToMany($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return ManyToMany
     */
    public function manyToMany($field, $entity, callable $callback = null)
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
     * @param \LaravelDoctrine\Fluent\Relations\Relation $relation
     * @param callable|null                              $callback
     *
     * @return Relation
     */
    public function addRelation(Relation $relation, callable $callback = null)
    {
        $this->callbackAndQueue($relation, $callback);

        return $relation;
    }

    /**
     * @return ClassMetadataBuilder
     */
    abstract public function getBuilder();

    /**
     * @param Buildable     $buildable
     * @param callable|null $callback
     */
    abstract protected function callbackAndQueue(Buildable $buildable, callable $callback = null);

    /**
     * @return NamingStrategy
     */
    abstract public function getNamingStrategy();
}
