<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\Mapper;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\Mappings\StubEntityMapping;

class EntityMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityMapper
     */
    protected $mapper;

    protected function setUp()
    {
        $mapping      = new StubEntityMapping;
        $this->mapper = new EntityMapper($mapping);
    }

    public function test_it_should_be_a_mapper()
    {
        $this->assertInstanceOf(Mapper::class, $this->mapper);
    }

    public function test_it_should_not_be_transient()
    {
        $this->assertFalse($this->mapper->isTransient());
    }

    public function test_it_should_delegate_the_proper_mapping_to_the_mapping_class()
    {
        $metadata = new ClassMetadataInfo(StubEntity::class);
        $builder  = new Builder(new ClassMetadataBuilder($metadata));

        $this->mapper->map($builder);

        $this->assertContains('id', $metadata->fieldNames);
        $this->assertContains('name', $metadata->fieldNames);
        $this->assertContains(StubEntity::class, $metadata->associationMappings['parent']['targetEntity']);
        $this->assertContains(StubEntity::class, $metadata->associationMappings['children']['targetEntity']);
    }
}
