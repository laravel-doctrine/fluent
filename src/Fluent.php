<?php

namespace LaravelDoctrine\Fluent;

interface Fluent
{
    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Table
     */
    public function table($name, callable $callback = null);

    /**
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Entity
     */
    public function entity(callable $callback = null);

    /**
     * @param          $type
     * @param          $name
     * @param callable $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function field($type, $name, callable $callback = null);

    /**
     * @param               $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function increments($name, callable $callback = null);

    /**
     * @param          $name
     * @param callable $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function string($name, callable $callback = null);

    /**
     * @return bool
     */
    public function isEmbeddedClass();
}
