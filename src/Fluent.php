<?php

namespace LaravelDoctrine\Fluent;

/**
 * @method Builders\Field array($name, callable $callback = null)
 *
 * Extensions:
 * @method void                              loggable(string $logEntry = null)
 * @method Extensions\Gedmo\SoftDeleteable   softDelete(string $fieldName = 'deletedAt', string $type = 'dateTime')
 * @method void                              timestamps(string $createdAt = 'createdAt', string $updatedAt = 'updatedAt', string $type = 'dateTime')
 * @method Extensions\Gedmo\TranslationClass translationClass(string $class)
 * @method Extensions\Gedmo\Tree             tree(callable $callback = null)
 * @method Extensions\Gedmo\Uploadable       uploadable()
 * @method                                   locale(string $fieldName)
 */
interface Fluent extends Buildable
{
    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return Builders\Table
     */
    public function table($name, callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Builders\Entity
     */
    public function entity(callable $callback = null);

    /**
     * @param array|string $columns
     *
     * @return Builders\Index
     */
    public function index($columns);

    /**
     * @param array|string $columns
     *
     * @return Builders\UniqueConstraint
     */
    public function unique($columns);

    /**
     * @param string        $type
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function field($type, $name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function increments($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function smallIncrements($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function bigIncrements($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function string($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function text($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function integer($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function smallInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function bigInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function unsignedSmallInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function unsignedInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function unsignedBigInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function float($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function decimal($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function boolean($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function jsonArray($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function date($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function dateTime($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function dateTimeTz($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function time($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function carbonDateTime($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function carbonDateTimeTz($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function carbonDate($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function carbonTime($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function zendDate($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function timestamp($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function timestampTz($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function binary($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function rememberToken($name = 'rememberToken', callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\OneToOne
     */
    public function hasOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\OneToOne
     */
    public function oneToOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\ManyToOne
     */
    public function belongsTo($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\ManyToOne
     */
    public function manyToOne($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\OneToMany
     */
    public function hasMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\OneToMany
     */
    public function oneToMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\ManyToMany
     */
    public function belongsToMany($entity, $field = null, callable $callback = null);

    /**
     * @param string        $entity
     * @param string        $field
     * @param callable|null $callback
     *
     * @return Relations\ManyToMany
     */
    public function manyToMany($entity, $field, callable $callback = null);

    /**
     * Adds a custom relation to the entity.
     *
     * @param Relations\Relation $relation
     * @param callable|null      $callback
     *
     * @return Relations\Relation
     */
    public function addRelation(Relations\Relation $relation, callable $callback = null);

    /**
     * @return bool
     */
    public function isEmbeddedClass();

    /**
     * @return \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder
     */
    public function getBuilder();

    /**
     * @param string        $method
     * @param callable|null $callback
     */
    public static function macro($method, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function guid($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function blob($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function object($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Builders\Field
     */
    public function simpleArray($name, callable $callback = null);

    /**
     * @param string        $embeddable
     * @param string|null   $field
     * @param callable|null $callback
     *
     * @return Builders\Embedded
     */
    public function embed($embeddable, $field = null, callable $callback = null);

    /**
     * @param string        $type
     * @param callable|null $callback
     *
     * @return Builders\Inheritance\Inheritance
     */
    public function inheritance($type, callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Builders\Inheritance\Inheritance
     */
    public function singleTableInheritance(callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Builders\Inheritance\Inheritance
     */
    public function joinedTableInheritance(callable $callback = null);

    /**
     * @param array|string $fields
     *
     * @return Builders\Primary
     */
    public function primary($fields);

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return Builders\Overrides\Override
     */
    public function override($name, callable $callback);

    /**
     * @param callable|null $callback
     *
     * @return Builders\LifecycleEvents
     */
    public function events(callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return Builders\EntityListeners
     */
    public function listen(callable $callback = null);
}
