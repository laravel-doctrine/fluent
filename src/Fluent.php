<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\Relation;

interface Fluent
{
    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Table
     */
    public function table($name, callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Entity
     */
    public function entity(callable $callback = null);

    /**
     * @param          $type
     * @param          $name
     * @param callable $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function field($type, $name, callable $callback = null);

    /**
     * @param               $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function increments($name, callable $callback = null);

    /**
     * @param          $name
     * @param callable $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function string($name, callable $callback = null);

    /**
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($field, $entity, callable $callback = null);

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($field, $entity, callable $callback = null);

    /**
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($field, $entity, callable $callback = null);

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return OneToMany
     */
    public function oneToMany($field, $entity, callable $callback = null);

    /**
     * Adds a custom relation to the entity.
     *
     * @param \LaravelDoctrine\Fluent\Relations\Relation $relation
     * @param callable|null                              $callback
     *
     * @return Relation
     */
    public function addRelation(Relation $relation, callable $callback = null);

    /**
     * @return bool
     */
    public function isEmbeddedClass();

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder();

    /**
     * @param ClassMetadataBuilder $builder
     */
    public function setBuilder(ClassMetadataBuilder $builder);

    /**
     * @param NamingStrategy $namingStrategy
     */
    public function setNamingStrategy(NamingStrategy $namingStrategy);

    /**
     * @param string        $method
     * @param callable|null $callback
     */
    public function extend($method, callable $callback = null);

    /**
     * @return array|Field[]
     */
    public function getPendingFields();

    /**
     * @return array|Relation[]
     */
    public function getPendingRelations();
}
