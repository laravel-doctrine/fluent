<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\Common\Inflector\Inflector;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;

trait Relations
{
    /**
     * {@inheritdoc}
     */
    public function hasOne($entity, $field = null, callable $callback = null)
    {
        return $this->oneToOne($entity, $field, $callback)->ownedBy(
            $this->guessSingularField($entity)
        );
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function belongsTo($entity, $field = null, callable $callback = null)
    {
        return $this->manyToOne($entity, $field, $callback);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function hasMany($entity, $field = null, callable $callback = null)
    {
        return $this->oneToMany($entity, $field, $callback);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function belongsToMany($entity, $field = null, callable $callback = null)
    {
        return $this->manyToMany($entity, $field, $callback);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
        return $field ?: Inflector::singularize(
            lcfirst(basename(str_replace('\\', '/', $entity)))
        );
    }

    /**
     * @param string      $entity
     * @param string|null $field
     *
     * @return string
     */
    protected function guessPluralField($entity, $field = null)
    {
        return $field ?: Inflector::pluralize($this->guessSingularField($entity));
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
