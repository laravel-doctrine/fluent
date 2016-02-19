<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use InvalidArgumentException;

trait Macroable
{
    /**
     * @var callable[]
     */
    protected static $macros = [];

    /**
     * @param string        $method
     * @param callable|null $callback
     */
    public static function macro($method, callable $callback = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Macros should be used with a closure argument, none given');
        }

        self::$macros[$method] = $callback;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function hasMacro($method)
    {
        return isset(self::$macros[$method]);
    }

    /**
     * @param string $method
     *
     * @return callable
     */
    protected function getMacro($method)
    {
        return self::$macros[$method];
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    protected function callMacro($method, array $params = [])
    {
        // Add builder as first closure param, append the given params
        array_unshift($params, $this);

        return call_user_func_array($this->getMacro($method), $params);
    }
}
