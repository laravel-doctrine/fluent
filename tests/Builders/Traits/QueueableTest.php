<?php

namespace Tests\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Builders\Traits\Queueable;
use LaravelDoctrine\Fluent\Builders\Traits\QueuesMacros;

/**
 * @covers \LaravelDoctrine\Fluent\Builders\Traits\QueuesMacros
 */
class QueueableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueableClass
     */
    private $queueable;

    protected function setUp()
    {
        $this->queueable = new QueueableClass();
    }

    public function test_can_queue_buildables()
    {
        $this->queueable->addToQueue(\Mockery::mock(Buildable::class));
        $this->queueable->addToQueue(\Mockery::mock(Buildable::class));

        $this->assertCount(2, $this->queueable->getQueued());
    }

    public function test_can_queue_and_callback_buildables()
    {
        $buildable = \Mockery::mock(Buildable::class, ['callMe' => true]);
        $buildable->shouldReceive('callMe')->once();

        $this->queueable->callbackAndQueue($buildable, function ($buildable) {
            $buildable->callMe();
        });

        $this->assertCount(1, $this->queueable->getQueued());
    }

    public function test_it_should_build_the_queued_buildables()
    {
        /** @var Buildable|\Mockery\Mock $buildable */
        $buildable = \Mockery::mock(Buildable::class);
        /** @var Buildable|\Mockery\Mock $buildable2 */
        $buildable2 = \Mockery::mock(Buildable::class);

        $buildable->shouldReceive('build')->once();
        $buildable2->shouldReceive('build')->once();

        $this->queueable->addToQueue($buildable);
        $this->queueable->addToQueue($buildable2);

        $this->queueable->build();
    }

    public function test_it_should_build_the_delayed_queued_buildables()
    {
        $buildable = new BuildableBeforeDelay();
        /** @var Delay|Buildable|\Mockery\Mock $delayed */
        $delayed = new DelayedClass();

        $this->queueable->addToQueue($buildable);
        $this->queueable->addToQueue($delayed);

        $this->queueable->build();
    }

    public function test_macro_inception_doesnt_get_the_buildable_built_twice()
    {
        $mock = \Mockery::mock(Buildable::class);
        $mock->shouldReceive('build')->once();

        QueueableClass::macro('firstLevel', function() use ($mock) {
            return $mock;
        });

        QueueableClass::macro('inception', function(QueueableClass $builder){
            return $builder->firstLevel();
        });

        $this->queueable->inception();
        $this->queueable->build();
    }
}

class QueueableClass
{
    use Queueable, Macroable, QueuesMacros;

    public function addToQueue(Buildable $buildable)
    {
        $this->queue($buildable);
    }

    public function __call($name, $arguments)
    {
        if ($this->hasMacro($name)) {
            return $this->queueMacro($name, $arguments);
        }
    }
}

class DelayedClass implements Buildable, Delay
{
    public static $expectation;

    public function build()
    {
        self::$expectation->wasCalled();
    }
}

class BuildableBeforeDelay implements Buildable
{
    public function build()
    {
        DelayedClass::$expectation = \Mockery::mock(['wasCalled' => true]);
        DelayedClass::$expectation->shouldReceive('wasCalled')->once();
    }
}
