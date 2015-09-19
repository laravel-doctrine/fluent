<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

interface Relation
{
    /**
     * @param array $cascade
     * @Enum({"persist", "remove", "merge", "detach", "refresh", "ALL"})
     *
     * @return $this
     */
    public function cascade(array $cascade);

    /**
     * @param $strategy
     * @Enum({"LAZY", "EAGER", "EXTRA_LAZY"})
     *
     * @return $this
     */
    public function fetch($strategy);

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder();

    /**
     * @return AssociationBuilder
     */
    public function getAssociation();

    /**
     * Build the association
     */
    public function build();
}
