<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;

trait Owning
{
    /**
     * @param string $relation
     *
     * @return $this
     */
    public function owning($relation)
    {
        $this->getAssociation()->inversedBy($relation);

        return $this;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();
}
