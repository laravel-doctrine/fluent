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

        $this->entity->isReadOnly();

        $this->assertTrue($this->builder->getClassMetadata()->isReadOnly);
    }
}
