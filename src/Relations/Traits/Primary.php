<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

/**
 * @method $this makePrimaryKey()
 */
trait Primary
{
    /**
     * @return AssociationBuilder
     */
    public function primary()
    {
        $this->getAssociation()->makePrimaryKey();

        return $this;
    }
}
