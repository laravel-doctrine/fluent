<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

trait Orderable
{
    /**
     * @param string $name
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($name, $order = 'ASC')
    {
        $this->getAssociation()->setOrderBy([
            $name => $order,
        ]);

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder
     */
    abstract public function getAssociation();
}
