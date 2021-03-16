<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use Tests\Relations\Traits\Indexable;
use Tests\Relations\Traits\NonPrimary;
use Tests\Relations\Traits\OneTo;
use Tests\Relations\Traits\Orderable;
use Tests\Relations\Traits\Ownable;

class OneToManyTest extends RelationTestCase
{
    use OneTo, Indexable, Orderable, Ownable, NonPrimary;

    /**
     * @var OneToMany
     */
    protected $relation;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $field = 'children';

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        // OneToMany needs to have the parent to be mapped
        $relation = new ManyToOne($this->builder, new DefaultNamingStrategy(), 'parent', FluentEntity::class);
        $relation->build();

        $this->relation = new OneToMany($this->builder, new DefaultNamingStrategy(), $this->field, FluentEntity::class);
        $this->relation->mappedBy('parent');
    }
}
