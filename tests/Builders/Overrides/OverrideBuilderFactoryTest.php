<?php

namespace Tests\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Overrides\AssociationOverride;
use LaravelDoctrine\Fluent\Builders\Overrides\AttributeOverride;
use LaravelDoctrine\Fluent\Builders\Overrides\OverrideBuilderFactory;
use Tests\Stubs\Entities\StubEntity;

class OverrideBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_create_attribute_override()
    {
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $builder->addField('attribute', 'string');

        $override = OverrideBuilderFactory::create(
            $builder,
            new DefaultNamingStrategy(),
            'attribute',
            function ($attribute) {
                return $attribute;
            }
        );

        $this->assertInstanceOf(AttributeOverride::class, $override);
    }

    public function test_can_create_association_override()
    {
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $builder->addOwningManyToMany('owner', StubEntity::class);

        $override = OverrideBuilderFactory::create(
            $builder,
            new DefaultNamingStrategy(),
            'owner',
            function ($relation) {
                return $relation;
            }
        );

        $this->assertInstanceOf(AssociationOverride::class, $override);
    }

    public function test_can_only_create_overrides_for_existing_attributes_or_relations()
    {
        $this->setExpectedException(InvalidArgumentException::class,  'No attribute or association could be found for some_field');

        $builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        OverrideBuilderFactory::create(
            $builder,
            new DefaultNamingStrategy(),
            'some_field',
            function ($relation) {
                return $relation;
            }
        );
    }
}
