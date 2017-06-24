<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;
use LaravelDoctrine\Fluent\Fluent;

abstract class TreeStrategy implements Buildable, Extension
{
    /**
     * @var string
     */
    protected $parent;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var Fluent
     */
    protected $builder;

    /**
     * @param Fluent $builder
     */
    public function __construct(Fluent $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Enable extension.
     */
    public static function enable()
    {
        TreeLevel::enable();
        TreeSelfReference::enableParent();
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @throws InvalidMappingException
     *
     * @return $this
     */
    public function level($field = 'level', $type = 'integer', callable $callback = null)
    {
        $this->validateNumericField($type, $field);

        $this->mapField($type, $field, $callback);

        $this->level = $field;

        return $this;
    }

    /**
     * @param string        $field
     * @param callable|null $callback
     *
     * @return $this
     */
    public function parent($field = 'parent', callable $callback = null)
    {
        $this->addSelfReferencingRelation($field, $callback);

        $this->parent = $field;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $this->defaults();

        $this->getClassMetadata()->mergeExtension($this->getExtensionName(), $this->getValues());
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    protected function getExtensionName()
    {
        return FluentDriver::EXTENSION_NAME;
    }

    /**
     * @param string        $type
     * @param string        $field
     * @param callable|null $callback
     * @param bool|false    $nullable
     */
    protected function mapField($type, $field, callable $callback = null, $nullable = false)
    {
        $this->builder->field($type, $field, $callback)->nullable($nullable);
    }

    /**
     * @param string $type
     * @param string $field
     *
     * @throws InvalidMappingException
     */
    protected function validateNumericField($type, $field)
    {
        if (!in_array($type, ['integer', 'bigint', 'smallint'])) {
            throw new InvalidMappingException("Invalid type [$type] for the [$field] field. Must be a (small, big) integer type.");
        }
    }

    /**
     * Returns the name of the mapped class.
     *
     * @return string
     */
    protected function myself()
    {
        return $this->getClassMetadata()->name;
    }

    /**
     * @param string        $field
     * @param callable|null $callback
     */
    protected function addSelfReferencingRelation($field, callable $callback = null)
    {
        $this->builder->belongsTo($this->myself(), $field, $callback)->nullable();
    }

    /**
     * @return ExtensibleClassMetadata
     */
    protected function getClassMetadata()
    {
        return $this->builder->getBuilder()->getClassMetadata();
    }

    /**
     * @return array
     */
    protected function getValues()
    {
        $values = [];

        if ($this->parent) {
            $values['parent'] = $this->parent;
        }

        if ($this->level) {
            $values['level'] = $this->level;
        }

        return $values;
    }

    /**
     * Check if a given key is already configured for this extension.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function alreadyConfigured($key)
    {
        $config = $this->getClassMetadata()->getExtension($this->getExtensionName());

        return isset($config[$key]);
    }

    /**
     * Check if parent is missing from both configuration and the current tree strategy builder.
     *
     * @return bool
     */
    protected function isMissingParent()
    {
        return !$this->alreadyConfigured('parent') && !$this->parent;
    }

    /**
     * Set the default fields to fill if they were not configured.
     * All Tree strategies share one common required field: the parent reference.
     *
     * @return void
     */
    protected function defaults()
    {
        if ($this->isMissingParent()) {
            $this->parent();
        }
    }
}
