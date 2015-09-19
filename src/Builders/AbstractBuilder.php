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
     * @param ClassMetadataBuilder $builder
     */
    public function __construct(ClassMetadataBuilder $builder)
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
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->builder->getClassMetadata();
    }
}
