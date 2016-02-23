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
        $this->callIfCallable($callback, $buildable);

        $this->queue($buildable);
    }

    /**
     * Execute the build process for all queued buildables
     */
    public function build()
    {
        /** @var Buildable[] $queued */
        $queued = $this->getQueued();

        usort($queued, function (Buildable $buildable) {
            return $buildable instanceof Delay ? 1 : 0;
        });

        foreach ($queued as $buildable) {
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

    /**
     * Call the callable... only if it is really one.
     *
     * @param callable|null $callback
     * @param mixed         $builder
     *
     * @return void
     */
    protected function callIfCallable($callback, $builder)
    {
        if (is_callable($callback)) {
            $callback($builder);
        }
    }
}
