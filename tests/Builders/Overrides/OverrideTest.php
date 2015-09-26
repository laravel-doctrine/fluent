<?php

namespace Tests\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Builders\Overrides\Override;
use Tests\Stubs\Entities\StubEntity;

class OverrideTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_build_attribute_override()
    {
        $meta = new ClassMetadataInfo(
            StubEntity::class
        );

        $builder = new ClassMetadataBuilder($meta);
        $builder->addField('attribute', 'string');

        $override = new Override(
            $builder,
            new DefaultNamingStrategy(),
            'attribute',
            function ($attribute) {
                return $attribute;
            }
        );

        $this->assertInstanceOf(Delay::class, $override);

        $override->build();
    }

    public function test_can_build_association_override()
    {
        $meta = new ClassMetadataInfo(
            StubEntity::class
        );

        $builder = new ClassMetadataBuilder($meta);

        $builder->addOwningManyToMany('owner', StubEntity::class);

        $override = new Override(
            $builder,
            new DefaultNamingStrategy(),
            'owner',
            function ($relation) {
                return $relation;
            }
        );

        $this->assertInstanceOf(Delay::class, $override);

        $override->build();
    }
}
