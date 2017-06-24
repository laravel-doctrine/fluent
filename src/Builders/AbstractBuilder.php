<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\NamingStrategy;

abstract class AbstractBuilder
{
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
     */
    public function __construct(ClassMetadataBuilder $builder, NamingStrategy $namingStrategy = null)
    {
        $this->builder = $builder;
        $this->namingStrategy = $namingStrategy ?: new DefaultNamingStrategy();
    }

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->builder->getClassMetadata();
    }

    /**
     * @return NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }
}
