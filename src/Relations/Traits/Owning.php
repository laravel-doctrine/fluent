<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

trait Owning
{
    /**
     * @param string $relation
     *
     * @return $this
     */
    public function owns($relation)
    {
        $this->getAssociation()->inversedBy($relation);

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Mapping\Builder\AssociationBuilder
     */
    abstract public function getAssociation();
}
