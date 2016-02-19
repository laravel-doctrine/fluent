<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;

trait Queueable
{
    /**
     * @var Buildable[]
     */
    protected $queued = [];

    /**
     * @param Buildable $buildable
     */
    protected function queue(Buildable $buildable)
    {
        $this->queued[] = $buildable;
    }

    /**
     * @param Buildable     $buildable
     * @param callable|null $callback
     */
    public function callbackAndQueue(Buildable $buildable, callable $callback = null)
    {
        if (is_callable($callback)) {
            $callback($buildable);
        }

        $this->queue($buildable);
    }

    /**
     * Execute the build process for all queued buildables
     */
    public function build()
    {
        $delayed = [];
        foreach ($this->getQueued() as $buildable) {
            if ($buildable instanceof Delay) {
                $delayed[] = $buildable;
            } else {
                $buildable->build();
            }
        }

        // We will delay some of the builds, because they
        // depend on the executing of the other builds
        foreach ($delayed as $buildable) {
            $buildable->build();
        }
    }

    /**
     * @return \LaravelDoctrine\Fluent\Buildable[]
     */
    public function getQueued()
    {
        return $this->queued;
    }
}
