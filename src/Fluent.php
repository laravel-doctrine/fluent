<?php

namespace LaravelDoctrine\Fluent;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Embedded;
use LaravelDoctrine\Fluent\Builders\Entity;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\Index;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\LifecycleEvents;
use LaravelDoctrine\Fluent\Builders\Overrides\Override;
use LaravelDoctrine\Fluent\Builders\Primary;
use LaravelDoctrine\Fluent\Builders\Table;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;

/**
 * @method $this array($name, callable $callback = null)
 */
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
     * @param array|string $columns
     *
     * @return \LaravelDoctrine\Fluent\Builders\Index
     */
    public function index($columns);

    /**
     * @param array|string $columns
     *
     * @return \LaravelDoctrine\Fluent\Builders\UniqueConstraint
     */
    public function unique($columns);

    /**
     * @param string        $type
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function field($type, $name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function increments($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function smallIncrements($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function bigIncrements($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function string($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function text($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function integer($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function smallInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function bigInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function unsignedSmallInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function unsignedInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function unsignedBigInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function float($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function decimal($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function boolean($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function jsonArray($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function date($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function dateTime($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function dateTimeTz($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function time($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function timestamp($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function timestampTz($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function binary($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function rememberToken($name = 'rememberToken', callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function hasOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function oneToOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function oneToMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function belongsToMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function manyToMany($entity, $field, callable $callback = null);

    /**
     * Adds a custom relation to the entity.
     *
     * @param Relation      $relation
     * @param callable|null $callback
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
     * @param string        $method
     * @param callable|null $callback
     */
    public static function macro($method, callable $callback = null);

    /**
     * @return Buildable[]
     */
    public function getQueued();

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function guid($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function blob($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function object($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function setArray($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function simpleArray($name, callable $callback = null);

    /**
     * @param string        $embeddable
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return Embedded
     */
    public function embed($embeddable, $field = null, callable $callback = null);

    /**
     * @param string        $type
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function inheritance($type, callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function singleTableInheritance(callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Inheritance
     */
    public function joinedTableInheritance(callable $callback = null);

    /**
     * @param array|string $fields
     *
     * @return Primary
     */
    public function primary($fields);

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return Override
     */
    public function override($name, callable $callback);

    /**
     * @param callable|null $callback
     *
     * @return LifecycleEvents
     */
    public function events(callable $callback = null);
}
