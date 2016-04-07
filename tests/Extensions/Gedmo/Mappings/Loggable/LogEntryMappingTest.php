<?php

namespace Tests\Extensions\Gedmo\Mappings\Loggable;

use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use LaravelDoctrine\Fluent\Builders\Entity;
use LaravelDoctrine\Fluent\Builders\Index;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Loggable\LogEntryMapping;
use Tests\Extensions\Gedmo\Mappings\MappingTestCase;

class LogEntryMappingTest extends MappingTestCase
{
    public function configureMocks()
    {
        /** @var \Mockery\Mock|Entity $entity */
        $entity = \Mockery::mock(Entity::class);
        $entity->shouldReceive('setRepositoryClass')->with(LogEntryRepository::class)->andReturnSelf();
        
        /** @var Index|\Mockery\Mock $index */
        $index = \Mockery::mock(Index::class);
        $index->shouldReceive('name')->with("log_class_lookup_idx")->once()->andReturnSelf();
        $index->shouldReceive('name')->with("log_date_lookup_idx")->once()->andReturnSelf();
        $index->shouldReceive('name')->with("log_user_lookup_idx")->once()->andReturnSelf();
        $index->shouldReceive('name')->with("log_version_lookup_idx")->once()->andReturnSelf();
        
        $this->builder->shouldReceive('table')->with('ext_log_entries')->andReturnSelf();
        $this->builder->shouldReceive('index')->with(["object_class"])->andReturn($index);
        $this->builder->shouldReceive('index')->with(["logged_at"])->andReturn($index);
        $this->builder->shouldReceive('index')->with(["username"])->andReturn($index);
        $this->builder->shouldReceive('index')->with(["object_id", "object_class", "version"])->andReturn($index);
        $this->builder->shouldReceive('entity')->andReturn($entity);
    }

    protected function getMappingClass()
    {
        return LogEntryMapping::class;
    }

    protected function getMappedClass()
    {
        return LogEntry::class;
    }
}
