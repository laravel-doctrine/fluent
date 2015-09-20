<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;

trait Orderable
{
    /**
     * @param        $name
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($name, $order = 'ASC')
    {
        $this->getAssociation()->setOrderBy([
            $name => $order
        ]);

        return $this;
    }

    /**
     * @return AssociationBuilder
     */
    abstract public function getAssociation();
}
