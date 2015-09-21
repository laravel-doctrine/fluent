<?php

namespace Tests;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\FluentDriver;
use LaravelDoctrine\Fluent\Mappers\EmbeddableMapper;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\MappedSuperClassMapper;
use Tests\Stubs\Embedabbles\StubEmbeddable;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\MappedSuperClasses\StubMappedSuperClass;
use Tests\Stubs\Mappings\StubEmbeddableMapping;
use Tests\Stubs\Mappings\StubEntityMapping;
use Tests\Stubs\Mappings\StubMappedSuperClassMapping;

class FluentDriverTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_load_metadata_for_entities_that_were_added_to_it()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new FakeClassMapping);
        $driver->loadMetadataForClass(
            FakeEntity::class,
            new ClassMetadataInfo(FakeEntity::class)
        );

        $this->assertInstanceOf(
            EntityMapper::class,
            $driver->getMappers()->getMapperFor(FakeEntity::class)
        );
    }

    public function test_it_should_load_metadata_for_embeddables_that_were_added_to_it()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new StubEmbeddableMapping);
        $driver->loadMetadataForClass(
            StubEmbeddable::class,
            new ClassMetadataInfo(StubEmbeddable::class)
        );

        $this->assertInstanceOf(
            EmbeddableMapper::class,
            $driver->getMappers()->getMapperFor(StubEmbeddable::class)
        );
    }

    public function test_it_should_load_metadata_for_mapped_super_classes_that_were_added_to_it()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new StubMappedSuperClassMapping);
        $driver->loadMetadataForClass(
            StubMappedSuperClass::class,
            new ClassMetadataInfo(StubMappedSuperClass::class)
        );

        $this->assertInstanceOf(
            MappedSuperClassMapper::class,
            $driver->getMappers()->getMapperFor(StubMappedSuperClass::class)
        );
    }

    public function test_it_should_load_metadata_for_mappings_passed_as_constructor_param()
    {
        $driver = new FluentDriver([
            StubEntityMapping::class,
            StubEmbeddableMapping::class,
            StubMappedSuperClassMapping::class
        ]);

        $driver->loadMetadataForClass(
            StubEntity::class,
            new ClassMetadataInfo(StubEntity::class)
        );
        $this->assertInstanceOf(
            EntityMapper::class,
            $driver->getMappers()->getMapperFor(StubEntity::class)
        );

        $driver->loadMetadataForClass(
            StubEmbeddable::class,
            new ClassMetadataInfo(StubEmbeddable::class)
        );
        $this->assertInstanceOf(
            EmbeddableMapper::class,
            $driver->getMappers()->getMapperFor(StubEmbeddable::class)
        );

        $driver->loadMetadataForClass(
            StubMappedSuperClass::class,
            new ClassMetadataInfo(StubMappedSuperClass::class)
        );
        $this->assertInstanceOf(
            MappedSuperClassMapper::class,
            $driver->getMappers()->getMapperFor(StubMappedSuperClass::class)
        );
    }

    public function test_can_add_array_of_new_mappings()
    {
        $driver = new FluentDriver;

        $driver->addMappings([
            FakeClassMapping::class,
            StubEntityMapping::class
        ]);

        $this->assertContains(
            FakeEntity::class,
            $driver->getAllClassNames()
        );

        $this->assertContains(
            StubEntity::class,
            $driver->getAllClassNames()
        );
    }

    public function test_the_given_mapping_class_should_exist()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Mapping class [Tests\DoesnExist] does not exist');

        $driver = new FluentDriver;

        $driver->addMappings([
            DoesnExist::class
        ]);
    }

    public function test_the_given_mapping_class_should_implement_mapping()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Mapping class [Tests\Stubs\Entities\StubEntity] should implement LaravelDoctrine\Fluent\Mapping');

        $driver = new FluentDriver;

        $driver->addMappings([
            StubEntity::class
        ]);
    }

    public function test_it_should_return_all_class_names_of_loaded_entities()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new FakeClassMapping);
        $driver->addMapping(new StubEntityMapping);

        $this->assertContains(
            FakeEntity::class,
            $driver->getAllClassNames()
        );

        $this->assertContains(
            StubEntity::class,
            $driver->getAllClassNames()
        );
    }

    public function test_entities_should_not_be_transient()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new FakeClassMapping);

        $this->assertFalse($driver->isTransient(FakeEntity::class));
    }

    public function test_embeddables_should_be_transient()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new StubEmbeddableMapping);

        $this->assertTrue($driver->isTransient(StubEmbeddable::class));
    }

    public function test_mapped_super_classes_should_not_be_transient()
    {
        $driver = new FluentDriver;

        $driver->addMapping(new StubMappedSuperClassMapping);

        $this->assertFalse($driver->isTransient(StubMappedSuperClass::class));
    }

    public function test_it_should_fail_when_asked_for_metadata_that_was_not_added_to_it()
    {
        $driver = new FluentDriver();

        $this->setExpectedException(
            MappingException::class,
            'Class [Tests\FakeEntity] does not have a mapping configuration. Make sure you create a Mapping class that extends either LaravelDoctrine\Fluent\EntityMapping, LaravelDoctrine\Fluent\EmbeddableMapping or LaravelDoctrine\Fluent\MappedSuperClassMapping. If you are using inheritance mapping, remember to create mappings for every child of the inheritance tree.'
        );

        $driver->loadMetadataForClass(
            FakeEntity::class,
            new ClassMetadataInfo(FakeEntity::class)
        );
    }

    public function test_can_get_builder()
    {
        $driver = new FluentDriver();
        $this->assertInstanceOf(Fluent::class, $driver->getBuilder());
        $this->assertInstanceOf(Builder::class, $driver->getBuilder());
    }

    public function test_can_set_custom_builder()
    {
        $driver = new FluentDriver([], null, new CustomBuilder());
        $this->assertInstanceOf(Fluent::class, $driver->getBuilder());
        $this->assertInstanceOf(CustomBuilder::class, $driver->getBuilder());
    }
}

class FakeClassMapping extends EntityMapping
{
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('name');
    }

    public function mapFor()
    {
        return FakeEntity::class;
    }
}

class FakeEntity
{
    protected $id, $name;
}

class CustomBuilder extends Builder
{
}
