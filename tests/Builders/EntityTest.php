<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Entity;
use Tests\Stubs\Entities\StubEntity;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Entity
     */
    protected $entity;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $this->entity  = new Entity($this->builder);
    }

    public function test_can_set_repository_class()
    {
        $this->entity->setRepositoryClass('CustomRepo');

        $this->assertEquals('CustomRepo', $this->builder->getClassMetadata()->customRepositoryClassName);
    }

    public function test_can_set_read_only()
    {
        $this->assertFalse($this->builder->getClassMetadata()->isReadOnly);

        $this->entity->readOnly();

        $this->assertTrue($this->builder->getClassMetadata()->isReadOnly);
    }

    public function test_can_enable_2nd_level_cache()
    {
        $this->assertFalse($this->builder->getClassMetadata()->isReadOnly);

        $this->entity->cacheable();
        $this->assertEquals(ClassMetadataInfo::CACHE_USAGE_READ_ONLY, $this->builder->getClassMetadata()->cache['usage']);
        $this->assertEquals('tests_stubs_entities_stubentity', $this->builder->getClassMetadata()->cache['region']);

        $this->entity->cacheable(ClassMetadataInfo::CACHE_USAGE_READ_WRITE, 'custom_region');
        $this->assertEquals(ClassMetadataInfo::CACHE_USAGE_READ_WRITE, $this->builder->getClassMetadata()->cache['usage']);
        $this->assertEquals('custom_region', $this->builder->getClassMetadata()->cache['region']);
    }
}
