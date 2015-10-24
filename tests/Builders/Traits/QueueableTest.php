<?php

namespace Tests\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Builders\Traits\Queueable;

class QueueableTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_queue_buildables()
    {
        $builder = new QueueableClass;
        $builder->queue(new BuildableClass);
        $builder->queue(new BuildableClass);

        $this->assertCount(2, $builder->getQueued());
    }

    public function test_can_queue_and_callback_buildables()
    {
        $builder = new QueueableClass;

        $called = 0;
        $builder->callbackAndQueue(new BuildableClass, function ($buildable) use (&$called) {
            $this->assertInstanceOf(Buildable::class, $buildable);
            $called++;
        });

        $builder->callbackAndQueue(new BuildableClass, function ($buildable) use (&$called) {
            $this->assertInstanceOf(Buildable::class, $buildable);
            $called++;
        });

        $this->assertEquals(2, $called);
        $this->assertCount(2, $builder->getQueued());
    }

    public function test_it_should_build_the_queued_buildables()
    {
        $buildable = new BuildableClass;

        $builder = new QueueableClass;
        $builder->queue($buildable);
        $builder->queue($buildable);

        $builder->build();

        $this->assertEquals(2, $buildable->getCalled());
    }

    public function test_it_should_build_the_delayed_queued_buildables()
    {
        $buildable = new BuildableClass;
        $delayed = new DelayedBuildableClass;

        $builder = new QueueableClass;
        $builder->queue($buildable);
        $builder->queue($delayed);

        $builder->build();

        $this->assertEquals(1, $buildable->getCalled());
        $this->assertEquals(1, $delayed->getCalled());
    }
}

class QueueableClass
{
    use Queueable;
}

class BuildableClass implements Buildable
{
    /**
     * @var int
     */
    protected $called;

    /**
     * @param int $called
     */
    public function __construct($called = 0)
    {
        $this->called = $called;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $this->called++;
    }

    /**
     * @return int
     */
    public function getCalled()
    {
        return $this->called;
    }
}


class DelayedBuildableClass extends BuildableClass implements Delay
{
}
