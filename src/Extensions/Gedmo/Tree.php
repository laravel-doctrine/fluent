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
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $strategy
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $strategy = 'nested')
    {
        $this->classMetadata = $classMetadata;
        $this->strategy      = $strategy;
    }

    /**
     * Enable extension
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Fluent $fluent, $strategy = 'nested') {
            return new static($fluent->getBuilder()->getClassMetadata(), $strategy);
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
     * Execute the build process
     */
    public function build()
    {
        if (!in_array($this->strategy, $this->strategies)) {
            throw new InvalidMappingException("Tree type: $this->strategy is not available.");
        }

        $this->classMetadata->appendExtension($this->getExtensionName(), [
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
}
