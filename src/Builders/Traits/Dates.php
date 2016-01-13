<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\DBAL\Types\Type;
use LaravelDoctrine\Fluent\Builders\Field;

trait Dates
{
    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function date($name, callable $callback = null)
    {
        return $this->field(Type::DATE, $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function dateTime($name, callable $callback = null)
    {
        return $this->field(Type::DATETIME, $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function dateTimeTz($name, callable $callback = null)
    {
        return $this->field(Type::DATETIMETZ, $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function time($name, callable $callback = null)
    {
        return $this->field(Type::TIME, $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function carbonDateTime($name, callable $callback = null)
    {
        return $this->field('carbondatetime', $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function carbonDateTimeTz($name, callable $callback = null)
    {
        return $this->field('carbondatetimetz', $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function carbonDate($name, callable $callback = null)
    {
        return $this->field('carbondate', $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function carbonTime($name, callable $callback = null)
    {
        return $this->field('carbontime', $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function zendDate($name, callable $callback = null)
    {
        return $this->field('zenddate', $name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function timestamp($name, callable $callback = null)
    {
        return $this->carbonDateTime($name, $callback);
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function timestampTz($name, callable $callback = null)
    {
        return $this->carbonDateTimeTz($name, $callback);
    }

    /**
     * @param string        $type
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    abstract public function field($type, $name, callable $callback = null);
}
