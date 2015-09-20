<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Mappers\EmbeddableMapper;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\MappedSuperClassMapper;
use LaravelDoctrine\Fluent\Mappers\MapperFactory;
use LaravelDoctrine\Fluent\Mapping;
use Tests\Stubs\Mappings\StubEmbeddableMapping;
use Tests\Stubs\Mappings\StubEntityMapping;
use Tests\Stubs\Mappings\StubMappedSuperClassMapping;

class MapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_create_entity_mapper()
    {
        $mapper = MapperFactory::create(new StubEntityMapping);

        $this->assertInstanceOf(EntityMapper::class, $mapper);
    }

    public function test_it_can_create_embeddable_mapper()
    {
        $mapper = MapperFactory::create(new StubEmbeddableMapping);

        $this->assertInstanceOf(EmbeddableMapper::class, $mapper);
    }

    public function test_it_can_create_mapped_super_class_mapper()
    {
        $mapper = MapperFactory::create(new StubMappedSuperClassMapping);

        $this->assertInstanceOf(MappedSuperClassMapper::class, $mapper);
    }

    public function test_can_only_create_mapper_when_mapAs_returns_a_valid_type()
    {
        $this->setExpectedException(
            MappingException::class,
            'Your mapping class should extend LaravelDoctrine\Fluent\EntityMapping, LaravelDoctrine\Fluent\MappedSuperClassMapping or LaravelDoctrine\Fluent\EmbeddableMapping'
        );

        MapperFactory::create(new WrongMapping);
    }
}

class WrongMapping implements Mapping
{
    /**
     * The given class should be mapped as Entity, Embeddable or MappedSuperClass
     *
     * @return string
     */
    public function mapAs()
    {
        return 'NonExistingType';
    }

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
    }

    /**
     * Returns the fully qualified name of the entity that this mapper maps.
     *
     * @return string
     */
    public function mapFor()
    {
        return 'wrong';
    }
}
