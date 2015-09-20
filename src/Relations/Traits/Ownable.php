<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;

trait Ownable
{
    /**
     * @param string $relation
     *
     * @return $this
     */
    public function ownedBy($relation)
    {
        $this->getAssociation()->mappedBy($relation);

        return $this;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();
}
