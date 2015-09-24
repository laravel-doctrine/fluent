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

    /**
     * @param string $schema
     *
     * @return $this
     */
    public function schema($schema)
    {
        $this->builder->getClassMetadata()->setPrimaryTable(['schema' => $schema]);

        return $this;
    }
}
