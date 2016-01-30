<?php

namespace LaravelDoctrine\Fluent\Builders\Traits;

trait Aliases
{
    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function increments($name, callable $callback = null)
    {
        $this->disallowInEmbeddedClasses();

        return $this->integer($name, $callback)->primary()->unsigned()->autoIncrement();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function smallIncrements($name, callable $callback = null)
    {
        $this->disallowInEmbeddedClasses();

        return $this->smallInteger($name, $callback)->primary()->unsigned()->autoIncrement();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function bigIncrements($name, callable $callback = null)
    {
        $this->disallowInEmbeddedClasses();

        return $this->bigInteger($name, $callback)->primary()->unsigned()->autoIncrement();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function unsignedSmallInteger($name, callable $callback = null)
    {
        return $this->smallInteger($name, $callback)->unsigned();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function unsignedInteger($name, callable $callback = null)
    {
        return $this->integer($name, $callback)->unsigned();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function unsignedBigInteger($name, callable $callback = null)
    {
        return $this->bigInteger($name, $callback)->unsigned();
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    public function rememberToken($name = 'rememberToken', callable $callback = null)
    {
        return $this->string($name, $callback)->nullable()->length(100);
    }

    /**
     * @param  string          $message
     * @throws \LogicException
     */
    abstract protected function disallowInEmbeddedClasses($message = "");

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    abstract public function integer($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    abstract public function smallInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    abstract public function bigInteger($name, callable $callback = null);

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return \LaravelDoctrine\Fluent\Builders\Field
     */
    abstract public function string($name, callable $callback = null);
}
