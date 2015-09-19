<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

abstract class AbstractBuilder
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * The constructor is protected to force factory method usage
     *
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
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->builder->getClassMetadata();
    }
}
