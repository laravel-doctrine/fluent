<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\Mapper;
use Mockery as m;
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

    public function test_it_should_build_the_queued_buildables()
    {
        $meta    = m::mock(ClassMetadataBuilder::class);
        $builder = $this->mockBuilder($meta);

        $builder->shouldReceive('getQueued')->andReturn([
            $buildable1 = m::mock(Buildable::class),
            $buildable2 = m::mock(Buildable::class)
        ]);

        $buildable1->shouldReceive('build')->once();
        $buildable2->shouldReceive('build')->once();

        $this->mapper->map($builder);
    }

    public function test_it_should_build_the_delayed_queued_buildables()
    {
        $meta    = m::mock(ClassMetadataBuilder::class);
        $builder = $this->mockBuilder($meta);

        $builder->shouldReceive('getQueued')->andReturn([
            $delayed = m::mock(Delay::class),
            $buildable2 = m::mock(Buildable::class)
        ]);

        $delayed->shouldReceive('build')->once();
        $buildable2->shouldReceive('build')->once();

        $this->mapper->map($builder);
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @param $meta
     *
     * @return m\MockInterface
     */
    protected function mockBuilder($meta)
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('getBuilder')->once()->andReturn($meta);
        $builder->shouldReceive('increments')->once();
        $builder->shouldReceive('string')->once();
        $builder->shouldReceive('belongsTo')->once()->andReturn(m::self());
        $builder->shouldReceive('hasMany')->once()->andReturn(m::self());
        $builder->shouldReceive('inversedBy')->once();
        $builder->shouldReceive('mappedBy')->once();

        return $builder;
    }
}
