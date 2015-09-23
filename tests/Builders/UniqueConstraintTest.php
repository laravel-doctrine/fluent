<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\UniqueConstraint;
use Tests\Stubs\Entities\StubEntity;

class UniqueConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $this->builder->setTable('stub_entities');
    }

    public function test_can_add_a_unique_constraint_for_one_column_without_passing_a_unique_constraint_name()
    {
        $index = new UniqueConstraint(
            $this->builder,
            ['name']
        );

        $this->assertCount(1, $index->getColumns());
        $this->assertContains('name', $index->getColumns());

        $index->build();

        $uniqueConstraints = $this->builder->getClassMetadata()->table['uniqueConstraints'];

        $this->assertTrue(isset($uniqueConstraints['stub_entities_name_unique']));
        $this->assertCount(1, $uniqueConstraints['stub_entities_name_unique']['columns']);
        $this->assertContains('name', $uniqueConstraints['stub_entities_name_unique']['columns']);
    }

    public function test_can_add_a_unique_constraint_for_multiple_column_without_passing_a_unique_constraint_name()
    {
        $index = new UniqueConstraint(
            $this->builder,
            ['name', 'address', 'email']
        );

        $this->assertCount(3, $index->getColumns());
        $this->assertContains('name', $index->getColumns());

        $index->build();

        $uniqueConstraints = $this->builder->getClassMetadata()->table['uniqueConstraints'];

        $this->assertTrue(isset($uniqueConstraints['stub_entities_name_address_email_unique']));
        $this->assertCount(3, $uniqueConstraints['stub_entities_name_address_email_unique']['columns']);
        $this->assertContains('name', $uniqueConstraints['stub_entities_name_address_email_unique']['columns']);
    }

    public function test_can_set_a_custom_unique_name()
    {
        $index = new UniqueConstraint(
            $this->builder,
            ['name']
        );

        $index->name('custom_unique');

        $this->assertCount(1, $index->getColumns());
        $this->assertEquals('custom_unique', $index->getName());

        $index->build();

        $uniqueConstraints = $this->builder->getClassMetadata()->table['uniqueConstraints'];

        $this->assertTrue(isset($uniqueConstraints['custom_unique']));
        $this->assertCount(1, $uniqueConstraints['custom_unique']['columns']);
    }
}
