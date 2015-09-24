<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

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
     * @return \Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder
     */
    abstract public function getAssociation();
}
