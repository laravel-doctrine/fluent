<?php

namespace LaravelDoctrine\Fluent\Builders;

class Entity extends AbstractBuilder
{
    /**
     * @param $class
     *
     * @return $this
     */
    public function setRepositoryClass($class)
    {
        $this->builder->setCustomRepositoryClass($class);

        return $this;
    }

    /**
     * @return $this
     */
    public function isReadOnly()
    {
        $this->builder->setReadOnly();

        return $this;
    }
}
