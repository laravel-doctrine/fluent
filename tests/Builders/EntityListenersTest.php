<?php

namespace Tests\Builders;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\EntityListeners;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\StubEntityListener;

class EntityListenersTest extends TestCase
{
    /**
     * @var EntityListeners
     */
    protected $builder;

    /**
     * @var ClassMetadataBuilder
     */
    protected $fluent;

    protected function setUp(): void
    {
        $this->fluent = new ClassMetadataBuilder(
            new ClassMetadataInfo(StubEntity::class)
        );

        $this->builder = new EntityListeners($this->fluent);
    }

    /**
     * @dataProvider eventsProvider
     *
     * @param string      $event
     * @param string      $listener
     * @param string      $expectedMethod
     * @param string|null $method
     */
    public function test_can_add_event_listeners($event, $listener, $expectedMethod, $method = null)
    {
        $this->builder->{$event}($listener, $method);

        $this->builder->build();

        $this->assertTrue(
            isset($this->fluent->getClassMetadata()->entityListeners[$event])
        );

        $this->assertCount(
            1, $this->fluent->getClassMetadata()->entityListeners[$event]
        );

        $this->assertEquals([
            [
                'class'  => $listener,
                'method' => $expectedMethod
            ]
        ], $this->fluent->getClassMetadata()->entityListeners[$event]);
    }

    public function test_can_add_multiple_entity_listeners_per_event()
    {
        $this->builder
            ->onClear(StubEntityListener::class, 'onClear')
            ->onClear(StubEntityListener::class, 'handle');

        $this->builder->build();

        $this->assertTrue(
            isset($this->fluent->getClassMetadata()->entityListeners['onClear'])
        );

        $this->assertCount(
            2, $this->fluent->getClassMetadata()->entityListeners['onClear']
        );

        $this->assertEquals([
            [
                'class'  => StubEntityListener::class,
                'method' => 'onClear'
            ],
            [
                'class'  => StubEntityListener::class,
                'method' => 'handle'
            ]
        ], $this->fluent->getClassMetadata()->entityListeners['onClear']);
    }

    /**
     * @return array
     */
    public function eventsProvider()
    {
        return [
            [Events::preRemove, StubEntityListener::class, 'preRemove', 'preRemove'],
            [Events::postRemove, StubEntityListener::class, 'handle', 'handle'],
            [Events::prePersist, StubEntityListener::class, 'handle', 'handle'],
            [Events::postPersist, StubEntityListener::class, 'handle', 'handle'],
            [Events::preUpdate, StubEntityListener::class, 'handle', 'handle'],
            [Events::postUpdate, StubEntityListener::class, 'handle', 'handle'],
            [Events::postLoad, StubEntityListener::class, 'handle', 'handle'],
            [Events::loadClassMetadata, StubEntityListener::class, 'handle', 'handle'],
            [Events::onClassMetadataNotFound, StubEntityListener::class, 'handle', 'handle'],
            [Events::preFlush, StubEntityListener::class, 'handle', 'handle'],
            [Events::onFlush, StubEntityListener::class, 'handle', 'handle'],
            [Events::postFlush, StubEntityListener::class, 'handle', 'handle'],
            [Events::onClear, StubEntityListener::class, 'handle', 'handle'],
            [Events::onClear, StubEntityListener::class, 'onClear', null],
        ];
    }
}
