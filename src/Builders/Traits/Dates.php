<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

use Doctrine\DBAL\Types\Types;

trait Dates
{
    /**
     * {@inheritdoc}
     */
    public function date($name, callable $callback = null)
    {
        return $this->field(Types::DATE_MUTABLE, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function dateTime($name, callable $callback = null)
    {
        return $this->field(Types::DATETIME_MUTABLE, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function dateTimeTz($name, callable $callback = null)
    {
        return $this->field(Types::DATETIMETZ_MUTABLE, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function time($name, callable $callback = null)
    {
        return $this->field(Types::TIME_MUTABLE, $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function carbonDateTime($name, callable $callback = null)
    {
        return $this->field('carbondatetime', $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function carbonDateTimeTz($name, callable $callback = null)
    {
        return $this->field('carbondatetimetz', $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function carbonDate($name, callable $callback = null)
    {
        return $this->field('carbondate', $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function carbonTime($name, callable $callback = null)
    {
        return $this->field('carbontime', $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function zendDate($name, callable $callback = null)
    {
        return $this->field('zenddate', $name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function timestamp($name, callable $callback = null)
    {
        return $this->carbonDateTime($name, $callback);
    }

    /**
     * {@inheritdoc}
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
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    abstract public function field($type, $name, callable $callback = null);
}
