<?php

namespace LaravelDoctrine\Fluent\Builders;

class Table extends AbstractBuilder
{
    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->builder->setTable($name);

        return $this;
    }
}
