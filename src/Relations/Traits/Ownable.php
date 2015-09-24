<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

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
     * @return \Doctrine\ORM\Mapping\Builder\AssociationBuilder
     */
    abstract public function getAssociation();
}
