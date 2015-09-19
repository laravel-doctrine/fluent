<?php

namespace tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class ManyToOneTest extends RelationTestCase
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

        $this->relation = new ManyToOne($this->builder, new DefaultNamingStrategy(), $this->field, FluentEntity::class);
    }

    public function test_can_set_nullable()
    {
        $this->relation->nullable();

        $this->assertTrue($this->relation->getJoinColumn()->isNullable());
    }

    public function test_can_set_unique()
    {
        $this->relation->unique();

        $this->assertTrue($this->relation->getJoinColumn()->isUnique());
    }

    public function test_can_set_onDelete()
    {
        $this->relation->onDelete('delete');

        $this->assertEquals('delete', $this->relation->getJoinColumn()->getOnDelete());
    }
}
