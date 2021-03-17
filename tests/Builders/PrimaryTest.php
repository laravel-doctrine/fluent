<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Primary;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;

class PrimaryTest extends TestCase
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

    public function test_can_add_primary_for_one_column()
    {
        $index = new Primary(
            $this->builder,
            ['id']
        );

        $this->assertCount(1, $index->getFields());
        $this->assertContains('id', $index->getFields());

        $index->build();

        $indexes = $this->builder->getClassMetadata()->getIdentifier();

        $this->assertCount(1, $indexes);
        $this->assertContains('id', $indexes);
    }

    public function test_can_add_primary_for_multiple_columns()
    {
        $index = new Primary(
            $this->builder,
            ['relation1', 'relation2']
        );

        $this->assertCount(2, $index->getFields());
        $this->assertContains('relation1', $index->getFields());
        $this->assertContains('relation2', $index->getFields());

        $index->build();

        $indexes = $this->builder->getClassMetadata()->getIdentifier();

        $this->assertCount(2, $indexes);
        $this->assertContains('relation1', $indexes);
        $this->assertContains('relation2', $indexes);
    }
}
