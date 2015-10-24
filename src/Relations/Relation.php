<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Buildable;

interface Relation extends Buildable
{
    /**
     * @param string[] $cascade one of "persist", "remove", "merge", "detach", "refresh", "ALL"
     *
     * @return $this
     */
    public function cascade(array $cascade);

    /**
     * @param string $strategy one of "LAZY", "EAGER", "EXTRA_LAZY"
     *
     * @return $this
     */
    public function fetch($strategy);

    /**
     * @param string      $usage
     * @param string|null $region
     *
     * @return AssociationBuilder
     */
    public function cache($usage = 'READ_ONLY', $region = null);

    /**
     * @return ClassMetadataBuilder
     */
    public function getBuilder();

    /**
     * @return AssociationBuilder
     */
    public function getAssociation();
}
