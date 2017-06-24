<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\NamingStrategy;
use LaravelDoctrine\Fluent\Buildable;

class Embedded extends AbstractBuilder implements Buildable
{
    /**
     * @var string
     */
    protected $embeddable;

    /**
     * @var string
     */
    protected $relation;

    /**
     * @var string|null
     */
    protected $columnPrefix = null;

    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param string               $relation
     * @param string               $embeddable
     */
    public function __construct(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy, $relation, $embeddable)
    {
        parent::__construct($builder, $namingStrategy);

        $this->embeddable = $embeddable;
        $this->relation = $relation;
    }

    /**
     * @param string|null $prefix
     *
     * @return Embedded
     */
    public function prefix($prefix)
    {
        $this->columnPrefix = $prefix;

        return $this;
    }

    /**
     * @return Embedded
     */
    public function noPrefix()
    {
        $this->columnPrefix = false;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->addEmbedded(
            $this->relation,
            $this->embeddable,
            $this->columnPrefix
        );
    }
}
