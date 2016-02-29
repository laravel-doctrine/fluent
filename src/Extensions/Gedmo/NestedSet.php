<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;
use LaravelDoctrine\Fluent\Fluent;

class NestedSet implements Buildable, Extension
{
    const MACRO_METHOD = 'nestedSet';

    /**
     * @var Fluent
     */
    protected $builder;

    /**
     * @var string
     */
    protected $left;

    /**
     * @var string
     */
    protected $right;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @param Fluent $builder
     */
    public function __construct(Fluent $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Enable extension
     */
    public static function enable()
    {
        TreeLeft::enable();
        TreeRight::enable();
        TreeLevel::enable();
        TreeRoot::enable();
        TreeParent::enable();
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @return $this
     * @throws InvalidMappingException
     */
    public function left($field = 'left', $type = 'integer', callable $callback = null)
    {
        $this->validateNumericField($type, $field);

        $this->mapField($type, $field, $callback);

        $this->left = $field;

        return $this;
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @return $this
     * @throws InvalidMappingException
     */
    public function right($field = 'right', $type = 'integer', callable $callback = null)
    {
        $this->validateNumericField($type, $field);

        $this->mapField($type, $field, $callback);

        $this->right = $field;

        return $this;
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @return $this
     * @throws InvalidMappingException
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
    public function root($field = 'root', callable $callback = null)
    {
        $this->addSelfReferencingRelation($field, $callback);

        $this->root = $field;

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
     * Execute the build process
     */
    public function build()
    {
        $this->addDefaults();

        /** @var ExtensibleClassMetadata $classMetadata */
        $classMetadata = $this->builder->getBuilder()->getClassMetadata();
        $classMetadata->appendExtension($this->getExtensionName(), [
            'strategy' => 'nested',
            'left'     => $this->left,
            'right'    => $this->right,
            'level'    => $this->level,
            'root'     => $this->root,
            'parent'   => $this->parent,
        ]);
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
     * Add default values to all required fields.
     *
     * @return void
     */
    protected function addDefaults()
    {
        if (!$this->parent) {
            $this->parent();
        }

        if (!$this->left) {
            $this->left();
        }

        if (!$this->right) {
            $this->right();
        }
    }

    /**
     * @param string $type
     * @param string $field
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
        return $this->builder->getBuilder()->getClassMetadata()->name;
    }

    /**
     * @param string        $field
     * @param callable|null $callback
     */
    protected function addSelfReferencingRelation($field, callable $callback = null)
    {
        $this->builder->belongsTo($this->myself(), $field, $callback)->nullable();
    }
}
