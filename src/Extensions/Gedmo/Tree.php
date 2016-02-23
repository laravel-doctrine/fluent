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
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $strategy
     * @param bool                    $autoComplete
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $strategy = 'nested', $autoComplete = false)
    {
        $this->classMetadata = $classMetadata;
        $this->strategy      = $strategy;
        $this->autoComplete  = $autoComplete;
    }

    /**
     * Enable extension
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Fluent $fluent, $strategy = 'nested', $callback = null, $autoComplete = false) {
            $tree = new static($fluent->getBuilder()->getClassMetadata(), $strategy, $autoComplete);

            if (is_callable($callback)) {
                call_user_func($callback, $tree);
            }

            return $tree;
        });

        Builder::macro('nestedSet', function (Fluent $fluent, $callback = null) {
            return $fluent->tree('nested', $callback, true);
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
     * @param  string $field
     * @param  string $type
     * @param  null   $name
     * @return $this
     */
    public function root($field = 'root', $type = 'integer', $name = null)
    {
        $this->mapField($field, $type, $name, true);

        $this->root = $field;

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $type
     * @param  null   $name
     * @return $this
     */
    public function left($field = 'left', $type = 'integer', $name = null)
    {
        $this->mapField($field, $type, $name);

        $this->left = $field;

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $type
     * @param  null   $name
     * @return $this
     */
    public function right($field = 'right', $type = 'integer', $name = null)
    {
        $this->mapField($field, $type, $name);

        $this->right = $field;

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $type
     * @param  null   $name
     * @return $this
     */
    public function level($field = 'level', $type = 'integer', $name = null)
    {
        $this->mapField($field, $type, $name);

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

        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'root'             => $this->root,
            'level'            => $this->level,
            'right'            => $this->right,
            'left'             => $this->left,
            'strategy'         => $this->strategy,
            'activate_locking' => $this->activateLocking,
            'locking_timeout'  => $this->lockingTimeout,
            'closure'          => $this->closure
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
     * @param  bool $activateLocking
     * @return Tree
     */
    public function activateLocking($activateLocking = true)
    {
        $this->activateLocking = $activateLocking;

        return $this;
    }

    /**
     * @param  string $strategy
     * @return Tree
     */
    public function strategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @param  int  $lockingTimeout
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
     * @param  string $closure
     * @return Tree
     */
    public function closure($closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @param      $field
     * @param      $type
     * @param      $name
     * @param bool $nullable
     */
    private function mapField($field, $type, $name, $nullable = false)
    {
        $this->classMetadata->mapField([
            'type'       => $type,
            'fieldName'  => $field,
            'columnName' => $name,
            'nullable'   => $nullable
        ]);
    }
}
