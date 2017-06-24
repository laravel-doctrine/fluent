<?php

namespace LaravelDoctrine\Fluent\Builders\Inheritance;

class SingleTableInheritance extends AbstractInheritance
{
    /**
     * Set inheritance type.
     */
    protected function setType()
    {
        $this->builder->setSingleTableInheritance();
    }
}
