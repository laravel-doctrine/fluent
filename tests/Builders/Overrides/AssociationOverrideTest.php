<?php

namespace Tests\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\MappingException;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Overrides\AssociationOverride;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use Tests\Stubs\Entities\StubEntity;

class AssociationOverrideTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $this->builder->addOwningManyToMany('manyToMany', StubEntity::class);
        $this->builder->createManyToOne('manyToOne', StubEntity::class)->build();
    }

    public function test_it_should_return_instance_of_relation()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'The callback should return an instance of LaravelDoctrine\Fluent\Relations\Relation'
        );

        $override = $this->override('manyToMany', function () {
            return 'string';
        });

        $override->build();
    }

    public function test_the_overridden_association_should_exist()
    {
        $this->setExpectedException(
            MappingException::class,
            'No mapping found for field \'nonExisting\' on class \'Tests\Stubs\Entities\StubEntity\'.'
        );

        $override = $this->override('nonExisting', function () {
        });

        $override->build();
    }

    public function test_can_only_override_many_to____relations()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Only ManyToMany and ManyToOne relations can be overridden'
        );

        $this->builder->addOwningOneToOne('oneToOne', StubEntity::class);

        $override = $this->override('oneToOne', function () {
        });

        $override->build();
    }

    public function test_can_change_join_table_on_many_to_many()
    {
        $this->assertEquals('stubentity_stubentity', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['name']);

        $override = $this->override('manyToMany', function (ManyToMany $relation) {
            return $relation->joinTable('differentJoinTable');
        });

        $override->build();

        $this->assertEquals('differentJoinTable', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['name']);
    }

    public function test_can_change_source_on_many_to_many()
    {
        $this->assertEquals('stubentity_source', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['joinColumns'][0]['name']);

        $override = $this->override('manyToMany', function (ManyToMany $relation) {
            return $relation->source('foreign_key');
        });

        $override->build();

        $this->assertEquals('foreign_key', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['joinColumns'][0]['name']);
    }

    public function test_can_change_target_on_many_to_many()
    {
        $this->assertEquals('stubentity_target', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['inverseJoinColumns'][0]['name']);

        $override = $this->override('manyToMany', function (ManyToMany $relation) {
            return $relation->target('inverse_key');
        });

        $override->build();

        $this->assertEquals('inverse_key', $this->builder->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['inverseJoinColumns'][0]['name']);
    }

    public function test_defaults_will_be_remembered_for_many_to_many()
    {
        $manyToMany = $this->builder->createManyToMany('otherManyToMany', StubEntity::class);
        $manyToMany->setJoinTable('default_join_table');
        $manyToMany->build();

        $this->assertEquals('default_join_table', $this->builder->getClassMetadata()->getAssociationMapping('otherManyToMany')['joinTable']['name']);

        $override = $this->override('otherManyToMany', function (ManyToMany $relation) {
            return $relation;
        });

        $override->build();

        $this->assertEquals('default_join_table', $this->builder->getClassMetadata()->getAssociationMapping('otherManyToMany')['joinTable']['name']);
    }

    public function test_can_change_source_on_many_to_one()
    {
        $this->assertEquals('id', $this->builder->getClassMetadata()->getAssociationMapping('manyToOne')['joinColumns'][0]['referencedColumnName']);

        $override = $this->override('manyToOne', function (ManyToOne $relation) {
            return $relation->source('local_key');
        });

        $override->build();

        $this->assertEquals('local_key', $this->builder->getClassMetadata()->getAssociationMapping('manyToOne')['joinColumns'][0]['referencedColumnName']);
    }

    public function test_can_change_target_on_many_to_one()
    {
        $this->assertEquals('manyToOne_id', $this->builder->getClassMetadata()->getAssociationMapping('manyToOne')['joinColumns'][0]['name']);

        $override = $this->override('manyToOne', function (ManyToOne $relation) {
            return $relation->target('foreign_key');
        });

        $override->build();

        $this->assertEquals('foreign_key', $this->builder->getClassMetadata()->getAssociationMapping('manyToOne')['joinColumns'][0]['name']);
    }

    /**
     * @param $field
     * @param $callback
     *
     * @return AssociationOverride
     */
    protected function override($field, $callback)
    {
        return new AssociationOverride(
            $this->builder,
            new DefaultNamingStrategy(),
            $field,
            $callback
        );
    }
}
