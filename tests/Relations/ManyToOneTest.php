<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Relations\JoinColumn;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use Tests\Builders\IsMacroable;
use Tests\Relations\Traits\Owning;
use Tests\Relations\Traits\Primary;

class ManyToOneTest extends RelationTestCase
{
    use Owning, Primary, IsMacroable;

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

    public function test_can_add_join_column()
    {
        $this->relation->addJoinColumn('children');

        $this->relation->build();

        $assoc = $this->getAssocValue($this->field, 'joinColumns')[1];

        $this->assertEquals('children_id', $assoc['name']);
        $this->assertEquals('id', $assoc['referencedColumnName']);
        $this->assertFalse($assoc['nullable']);
    }

    public function test_can_get_join_columns()
    {
        $this->assertCount(1, $this->relation->getJoinColumns());

        $this->relation->addJoinColumn('children');

        $this->assertCount(2, $this->relation->getJoinColumns());
    }

    public function test_can_get_join_column()
    {
        $joinColumn = $this->relation->getJoinColumn(function ($joinColumn) {
            $this->assertInstanceOf(JoinColumn::class, $joinColumn);
        });

        $this->assertInstanceOf(JoinColumn::class, $joinColumn);
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

    /**
     * Get the builder under test.
     *
     * @return Macroable
     */
    protected function getMacroableBuilder()
    {
        return $this->relation;
    }
}
