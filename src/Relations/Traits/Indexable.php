<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;

trait Indexable
{
    /**
     * @param string $index
     *
     * @return $this
     */
    public function indexBy($index)
    {
        $this->getAssociation()->setIndexBy($index);

        return $this;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();
}
