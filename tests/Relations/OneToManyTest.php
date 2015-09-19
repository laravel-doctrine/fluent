<?php

namespace tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;

class OneToManyTest extends RelationTestCase
{
    /**
     * @var OneToMany
     */
    protected $relation;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $field = 'children';

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        // OneToMany needs to have the parent to be mapped
        $relation = new ManyToOne($this->builder, new DefaultNamingStrategy(), 'parent', FluentEntity::class);
        $relation->build();

        $this->relation = new OneToMany($this->builder, new DefaultNamingStrategy(), $this->field, FluentEntity::class);
        $this->relation->mappedBy('parent');
    }

    public function test_can_call_magic_one_to_many_assoc_methods()
    {
        $this->relation->setIndexBy('parent_id');

        $this->relation->build();

        $this->assertEquals('parent_id', $this->getAssocValue($this->field, 'indexBy'));
    }

    public function test_can_order_one_to_many_associations()
    {
        $this->relation->orderBy('id', 'DESC');

        $this->relation->build();

        $this->assertEquals('DESC', $this->getAssocValue($this->field, 'orderBy')['id']);
    }
}
