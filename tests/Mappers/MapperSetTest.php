<?php

namespace tests\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Mappers\EmbeddableMapper;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\MapperSet;
use Tests\Stubs\Embedabbles\StubEmbeddable;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\Mappings\StubEmbeddableMapping;
use Tests\Stubs\Mappings\StubEntityMapping;

class MapperSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type MapperSet
     */
    protected $mapperSet;

    protected function setUp()
    {
        $this->mapperSet = new MapperSet;
        $this->mapperSet->add(new StubEntityMapping);
        $this->mapperSet->add(new StubEmbeddableMapping);
    }

    public function test_it_should_accumulate_mapping_implementations()
    {
        $this->assertCount(2, $this->mapperSet->getClassNames());

        $this->assertInstanceOf(EntityMapper::class, $this->mapperSet->getMapperFor(StubEntity::class));
        $this->assertInstanceOf(EmbeddableMapper::class, $this->mapperSet->getMapperFor(StubEmbeddable::class));
    }

    public function test_it_should_fail_when_asked_for_an_unmapped_class()
    {
        $this->setExpectedException(
            MappingException::class,
            'Class [baz] does not have a mapping configuration. Make sure you create a Mapping class for it that extends LaravelDoctrine\Fluent\Mapping and make sure your entity extends either LaravelDoctrine\Fluent\Entity or LaravelDoctrine\Fluent\Embeddable. If you are using inheritance mapping, remember to create mappings for every child of the inheritance tree'
        );

        $this->mapperSet->getMapperFor('baz');
    }

    public function test_it_should_return_an_array_of_mapped_classes()
    {
        $this->assertEquals([StubEntity::class, StubEmbeddable::class], $this->mapperSet->getClassNames());
    }

    public function test_can_check_if_has_mapper_for_class()
    {
        $this->assertTrue($this->mapperSet->hasMapperFor(StubEntity::class));
        $this->assertFalse($this->mapperSet->hasMapperFor('baz'));
    }

    public function test_can_check_if_has_mappers()
    {
        $set = new MapperSet;
        $this->assertFalse($set->hasMappers());

        $set->add(new StubEntityMapping);
        $this->assertTrue($set->hasMappers());
    }

    public function test_can_get_mappers()
    {
        $set = new MapperSet;
        $this->assertCount(0, $set->getMappers());

        $mapping = new StubEntityMapping;

        $set->add($mapping);
        $this->assertCount(1, $set->getMappers());

        // Mapping will only get registered once
        $set->add($mapping);
        $this->assertCount(1, $set->getMappers());
    }
}
