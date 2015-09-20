<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToOne;

class OneToOneTest extends RelationTestCase
{
    /**
     * @var ManyToOne
     */
    protected $relation;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $field = 'parent';

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        $this->relation = new OneToOne($this->builder, new DefaultNamingStrategy(), $this->field, FluentEntity::class);
    }
}
