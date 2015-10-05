<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Illuminate\Support\Str;
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
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function hasOne($entity, $field = null, callable $callback = null)
    {
        return $this->oneToOne($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function oneToOne($entity, $field = null, callable $callback = null)
    {
        return $this->addRelation(
            new OneToOne(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $this->guessSingularField($entity, $field),
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($entity, $field = null, callable $callback = null)
    {
        return $this->manyToOne($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($entity, $field = null, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToOne(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $this->guessSingularField($entity, $field),
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($entity, $field = null, callable $callback = null)
    {
        return $this->oneToMany($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function oneToMany($entity, $field = null, callable $callback = null)
    {
        return $this->addRelation(
            new OneToMany(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $this->guessPluralField($entity, $field),
                $entity
            ),
            $callback
        );
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function belongsToMany($entity, $field = null, callable $callback = null)
    {
        return $this->manyToMany($entity, $field, $callback);
    }

    /**
     * @param string        $entity
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function manyToMany($entity, $field = null, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToMany(
                $this->getBuilder(),
                $this->getNamingStrategy(),
                $this->guessPluralField($entity, $field),
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
     * @param string      $entity
     * @param string|null $field
     *
     * @return string
     */
    protected function guessSingularField($entity, $field = null)
    {
        return $field ?: Str::camel(class_basename($entity));
    }

    /**
     * @param string      $entity
     * @param string|null $field
     *
     * @return string
     */
    protected function guessPluralField($entity, $field = null)
    {
        return $field ?: Str::plural($this->guessSingularField($entity));
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
