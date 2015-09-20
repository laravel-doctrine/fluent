<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
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
     * @param ClassMetadataBuilder|null $builder
     */
    public function __construct(ClassMetadataBuilder $builder = null)
    {
        $this->builder = $builder;
    }

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    public function setBuilder(ClassMetadataBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param NamingStrategy $namingStrategy
     */
    public function setNamingStrategy(NamingStrategy $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->builder->getClassMetadata();
    }
}
