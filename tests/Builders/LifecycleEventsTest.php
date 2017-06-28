<?php

namespace Tests\Builders;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\LifecycleEvents;
use Tests\Stubs\Entities\StubEntity;

class LifecycleEventsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LifecycleEvents
     */
    protected $builder;

    /**
     * @var ClassMetadataBuilder
     */
    protected $fluent;

    protected function setUp()
    {
        $this->fluent = new ClassMetadataBuilder(
            new ClassMetadataInfo(StubEntity::class)
        );

        $this->builder = new LifecycleEvents($this->fluent);
    }

    public function test_pre_remove_event()
    {
        $this->doEventTest(Events::preRemove);
    }

    public function test_post_remove_event()
    {
        $this->doEventTest(Events::postRemove);
    }

    public function test_pre_persist_event()
    {
        $this->doEventTest(Events::prePersist);
    }

    public function test_post_persist_event()
    {
        $this->doEventTest(Events::postPersist);
    }

    public function test_pre_update_event()
    {
        $this->doEventTest(Events::preUpdate);
    }

    public function test_post_update_event()
    {
        $this->doEventTest(Events::postUpdate);
    }

    public function test_post_load_event()
    {
        $this->doEventTest(Events::postLoad);
    }

    public function test_pre_flush_event()
    {
        $this->doEventTest(Events::preFlush);
    }

    public function test_fluent_builder_method_should_exist()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $this->builder->onFlagerbert('breakStuff');
    }

    private function doEventTest($event)
    {
        $this->assertFalse(
            $this->fluent->getClassMetadata()->hasLifecycleCallbacks($event),
            "Event [$event] is already associated!"
        );

        for ($i = 0, $max = mt_rand(1, 5); $i < $max; ++$i) {
            $this->builder->$event(uniqid());
        }

        $this->builder->build();

        $this->assertTrue(
            $this->fluent->getClassMetadata()->hasLifecycleCallbacks($event),
            "Event [$event] was not associated!"
        );

        $this->assertCount(
            $max, $actual = $this->fluent->getClassMetadata()->getLifecycleCallbacks($event),
            "Expected [$max] events associated for [$event], got " . count($actual)
        );
    }
}
