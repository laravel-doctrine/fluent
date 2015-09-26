<?php

namespace Tests\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\Entities\StubEntity2;
use Tests\Stubs\Entities\StubEntity3;

class InheritanceTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Inheritance
     */
    protected $inheritance;

    public function test_can_set_discriminator_column()
    {
        $this->inheritance->column('discr');

        $discr = $this->builder->getClassMetadata()->discriminatorColumn;

        $this->assertEquals('discr', $discr['name']);
        $this->assertEquals('string', $discr['type']);
        $this->assertEquals(255, $discr['length']);
    }

    public function test_can_set_discriminator_column_custom_type()
    {
        $this->inheritance->column('discr', 'integer', 6);

        $discr = $this->builder->getClassMetadata()->discriminatorColumn;

        $this->assertEquals('discr', $discr['name']);
        $this->assertEquals('integer', $discr['type']);
        $this->assertEquals(6, $discr['length']);
    }

    public function test_can_set_single_map()
    {
        $this->inheritance->map('name', StubEntity::class);

        $map = $this->builder->getClassMetadata()->discriminatorMap;

        $this->assertEquals(StubEntity::class, $map['name']);
    }

    public function test_can_set_multiple_map()
    {
        $this->inheritance->map('name', StubEntity::class);
        $this->inheritance->map('name2', StubEntity2::class);
        $this->inheritance->map('name3', StubEntity3::class);

        $map = $this->builder->getClassMetadata()->discriminatorMap;

        $this->assertEquals(StubEntity::class, $map['name']);
        $this->assertEquals(StubEntity2::class, $map['name2']);
        $this->assertEquals(StubEntity3::class, $map['name3']);
    }

    public function test_can_set_map_as_array()
    {
        $this->inheritance->map([
            'stub1'  => StubEntity::class,
            'stub2'  => StubEntity2::class,
            'stub3'  => StubEntity3::class
        ]);

        $map = $this->builder->getClassMetadata()->discriminatorMap;

        $this->assertEquals(StubEntity::class, $map['stub1']);
        $this->assertEquals(StubEntity2::class, $map['stub2']);
        $this->assertEquals(StubEntity3::class, $map['stub3']);
    }
}
