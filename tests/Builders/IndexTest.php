<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Index;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;

class IndexTest extends TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $this->builder->setTable('stub_entities');
    }

    public function test_can_add_index_for_one_column_without_passing_a_index_name()
    {
        $index = new Index(
            $this->builder,
            ['name']
        );

        $this->assertCount(1, $index->getColumns());
        $this->assertContains('name', $index->getColumns());

        $index->build();

        $indexes = $this->builder->getClassMetadata()->table['indexes'];

        $this->assertTrue(isset($indexes['stub_entities_name_index']));
        $this->assertCount(1, $indexes['stub_entities_name_index']['columns']);
        $this->assertContains('name', $indexes['stub_entities_name_index']['columns']);
    }

    public function test_can_add_index_for_multiple_column_without_passing_a_index_name()
    {
        $index = new Index(
            $this->builder,
            ['name', 'address', 'email']
        );

        $this->assertCount(3, $index->getColumns());
        $this->assertContains('name', $index->getColumns());

        $index->build();

        $indexes = $this->builder->getClassMetadata()->table['indexes'];

        $this->assertTrue(isset($indexes['stub_entities_name_address_email_index']));
        $this->assertCount(3, $indexes['stub_entities_name_address_email_index']['columns']);
        $this->assertContains('name', $indexes['stub_entities_name_address_email_index']['columns']);
    }

    public function test_can_set_a_custom_index_name()
    {
        $index = new Index(
            $this->builder,
            ['name']
        );

        $index->name('custom_index');

        $this->assertCount(1, $index->getColumns());
        $this->assertEquals('custom_index', $index->getName());

        $index->build();

        $indexes = $this->builder->getClassMetadata()->table['indexes'];

        $this->assertTrue(isset($indexes['custom_index']));
        $this->assertCount(1, $indexes['custom_index']['columns']);
    }
}
