<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;
use LaravelDoctrine\Fluent\Fluent;

class Tree implements Buildable, Extension
{
    const MACRO_METHOD = 'tree';

    /**
     * List of tree strategies available
     *
     * @var array
     */
    private $strategies = [
        'nested',
        'closure',
        'materializedPath',
    ];

    /**
     * @var Fluent
     */
    private $builder;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $strategy;

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
     * @var bool
     */
    protected $activateLocking = false;

    /**
     * @var int
     */
    protected $lockingTimeout = 3;

    /**
     * @var string
     */
    protected $closure;

    /**
     * @var bool
     */
    private $autoComplete = false;

    /**
     * @param Fluent $builder
     * @param string $strategy
     * @param bool   $autoComplete
     */
    public function __construct(Fluent $builder, $strategy = 'nested', $autoComplete = false)
    {
        $this->builder       = $builder;
        $this->classMetadata = $builder->getBuilder()->getClassMetadata();
        $this->strategy      = $strategy;
        $this->autoComplete  = $autoComplete;
    }

    /**
     * Enable extension
     */
    public static function enable()
    {
        $macro = function (Fluent $fluent, $strategy = 'nested', $callback = null, $autoComplete = false) {
            $tree = new static($fluent, $strategy, $autoComplete);

            if (is_callable($callback)) {
                call_user_func($callback, $tree);
            }

            return $tree;
        };

        Builder::macro(self::MACRO_METHOD, $macro);
        Builder::macro('nestedSet', function (Fluent $fluent, $callback = null) use ($macro) {
            return $macro($fluent, 'nested', $callback, true);
        });

        TreeLeft::enable();
        TreeRight::enable();
        TreeLevel::enable();
        TreeRoot::enable();
        TreeParent::enable();
        TreePath::enable();
        TreePathSource::enable();
        TreePathHash::enable();
        TreeLockTime::enable();
    }

    /**
     * @param string      $field
     * @param string      $type
     * @param string|null $column
     *
     * @return $this
     */
    public function root($field = 'root', $type = 'integer', $column = null)
    {
        $this->mapField($type, $field, $column, true);

        $this->root = $field;

        return $this;
    }

    /**
     * @param string      $field
     * @param string      $type
     * @param string|null $column
     *
     * @return $this
     */
    public function left($field = 'left', $type = 'integer', $column = null)
    {
        $this->mapField($type, $field, $column);

        $this->left = $field;

        return $this;
    }

    /**
     * @param string      $field
     * @param string      $type
     * @param string|null $column
     *
     * @return $this
     */
    public function right($field = 'right', $type = 'integer', $column = null)
    {
        $this->mapField($type, $field, $column);

        $this->right = $field;

        return $this;
    }

    /**
     * @param string      $field
     * @param string      $type
     * @param string|null $column
     *
     * @return $this
     */
    public function level($field = 'level', $type = 'integer', $column = null)
    {
        $this->mapField($type, $field, $column);

        $this->level = $field;

        return $this;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        if (!in_array($this->strategy, $this->strategies)) {
            throw new InvalidMappingException("Tree type: $this->strategy is not available.");
        }

        if ($this->autoComplete) {
            $this->addDefaults();
        }

        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'root'             => $this->root,
            'level'            => $this->level,
            'right'            => $this->right,
            'left'             => $this->left,
            'strategy'         => $this->strategy,
            'activate_locking' => $this->activateLocking,
            'locking_timeout'  => $this->lockingTimeout,
            'closure'          => $this->closure,
        ]);
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return FluentDriver::EXTENSION_NAME;
    }

    /**
     * @param bool $activateLocking
     *
     * @return Tree
     */
    public function activateLocking($activateLocking = true)
    {
        $this->activateLocking = $activateLocking;

        return $this;
    }

    /**
     * @param string $strategy
     *
     * @return Tree
     */
    public function strategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @param int $lockingTimeout
     *
     * @return Tree
     */
    public function lockingTimeout($lockingTimeout)
    {
        if ($lockingTimeout < 1) {
            throw new InvalidMappingException("Tree Locking Timeout must be at least of 1 second.");
        }

        $this->lockingTimeout = $lockingTimeout;

        return $this;
    }

    /**
     * @param string $closure
     *
     * @return Tree
     */
    public function closure($closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @param string $type
     * @param string $field
     * @param string $column
     * @param bool   $nullable
     */
    private function mapField($type, $field, $column = null, $nullable = false)
    {
        $field = $this->builder->field($type, $field)->nullable($nullable);

        if ($column) {
            $field->name($column);
        }
    }

    /**
     * Add default values to all required fields.
     *
     * @return void
     */
    private function addDefaults()
    {
        if (!$this->root) {
            $this->root('root');
        }

        if (!$this->level) {
            $this->level('level');
        }

        if (!$this->left) {
            $this->left('left');
        }

        if (!$this->right) {
            $this->right('right');
        }
    }
}
