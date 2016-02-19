<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use LaravelDoctrine\Fluent\Buildable;

trait QueuesMacros
{
    /**
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    abstract protected function callMacro($method, array $params = []);

    /**
     * @param Buildable $buildable
     */
    abstract protected function queue(Buildable $buildable);

    /**
     * Intercept the Macro call and queue the result if it's a Buildable object.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function queueMacro($method, $args)
    {
        $result = $this->callMacro($method, $args);

        if ($result instanceof Buildable) {
            $this->queue($result);
        }

        return $result;
    }
}
