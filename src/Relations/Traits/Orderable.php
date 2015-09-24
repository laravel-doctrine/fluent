<?php

namespace LaravelDoctrine\Fluent\Relations\Traits;

use Doctrine\ORM\Mapping\Builder\OneToManyAssociationBuilder;

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
            $name => $order
        ]);

        return $this;
    }

    /**
     * @return OneToManyAssociationBuilder
     */
    abstract public function getAssociation();
}
