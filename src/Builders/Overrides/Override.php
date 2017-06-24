<?php

namespace LaravelDoctrine\Fluent\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;

class Override implements Buildable, Delay
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param string               $name
     * @param callable             $callback
     */
    public function __construct(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy, $name, callable $callback)
    {
        $this->builder = $builder;
        $this->callback = $callback;
        $this->name = $name;
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $builder = OverrideBuilderFactory::create(
            $this->builder,
            $this->namingStrategy,
            $this->name,
            $this->callback
        );

        $builder->build();
    }
}
