<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use LaravelDoctrine\Fluent\Buildable;

class GeneratedValue implements Buildable
{
    /**
     * @var FieldBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $strategy;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $initial = 1;

    /**
     * @var int
     */
    protected $size = 10;

    /**
     * @param FieldBuilder $builder
     * @param string       $strategy
     */
    public function __construct(FieldBuilder $builder, $strategy = 'AUTO')
    {
        $this->builder  = $builder;
        $this->strategy = $strategy;
    }

    /**
     * @param string $strategy
     *
     * @return $this
     */
    public function strategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $initial
     *
     * @return $this
     */
    public function initialValue($initial)
    {
        $this->initial = $initial;

        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function allocationSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $this->builder->generatedValue($this->strategy);

        $this->builder->setSequenceGenerator(
            $this->name ?: uniqid('seq_'),
            $this->size,
            $this->initial
        );
    }
}
