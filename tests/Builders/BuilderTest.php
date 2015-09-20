<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\Table;
use LaravelDoctrine\Fluent\Entity;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;
use LogicException;
use Tests\FakeEntity;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Builder
     */
    protected $fluent;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        $this->fluent = new Builder();
        $this->fluent->setBuilder($this->builder);
        $this->fluent->setNamingStrategy(new DefaultNamingStrategy());
    }

    public function test_can_get_builder()
    {
        $entity = new Builder();
        $entity->setBuilder($this->builder);

        $this->assertInstanceOf(ClassMetadataBuilder::class, $entity->getBuilder());
    }

    public function test_can_create_entity()
    {
        $entity = new Builder();
        $entity->setBuilder($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertFalse($entity->getClassMetadata()->isEmbeddedClass);
        $this->assertFalse($entity->getClassMetadata()->isMappedSuperclass);
    }

    public function test_can_create_embeddable()
    {
        $entity = new Builder();
        $this->builder->setEmbeddable();
        $entity->setBuilder($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertTrue($entity->getClassMetadata()->isEmbeddedClass);
    }

    public function test_can_create_superclass()
    {
        $entity = new Builder();
        $this->builder->setMappedSuperClass();
        $entity->setBuilder($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertFalse($entity->getClassMetadata()->isEmbeddedClass);
        $this->assertTrue($entity->getClassMetadata()->isMappedSuperclass);
    }

    public function test_can_set_table_settings()
    {
        $table = $this->fluent->table('users');

        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals('users', $table->getClassMetadata()->getTableName());

        // Fluently change name
        $table->setName('users2');
        $this->assertEquals('users2', $table->getClassMetadata()->getTableName());

        // Closure usage
        $table = $this->fluent->table(function ($table) {
            $this->assertInstanceOf(Table::class, $table);
            $table->setName('users3');
        });

        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals('users3', $table->getClassMetadata()->getTableName());

        // Closure2 usage
        $table = $this->fluent->table('users4', function ($table) {
            $this->assertInstanceOf(Table::class, $table);
        });

        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals('users4', $table->getClassMetadata()->getTableName());
    }

    public function test_cannot_use_table_settings_for_embeddable()
    {
        $fluent = new Builder();
        $this->builder->setEmbeddable();
        $fluent->setBuilder($this->builder);

        $this->setExpectedException(LogicException::class);

        $fluent->table('users');
    }

    public function test_can_set_entity_settings()
    {
        $entity = $this->fluent->entity(function ($entity) {
            $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
        });

        $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
    }

    public function test_cannot_use_entity_settings_for_embeddable()
    {
        $fluent = new Builder();
        $this->builder->setEmbeddable();
        $fluent->setBuilder($this->builder);

        $this->setExpectedException(LogicException::class);

        $fluent->entity();
    }

    public function test_can_create_field()
    {
        $field = $this->fluent->field('integer', 'id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_can_add_increments_to_entity()
    {
        $field = $this->fluent->increments('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getIdentifier());
        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_cannot_add_increments_to_embeddable()
    {
        $fluent = new Builder();
        $this->builder->setEmbeddable();
        $fluent->setBuilder($this->builder);

        $this->setExpectedException(LogicException::class);

        $fluent->increments('id');
    }

    public function test_can_add_string()
    {
        $field = $this->fluent->string('name', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('name', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_can_add_relation()
    {
        $relation = $this->fluent->addRelation(new OneToMany(
            $this->builder,
            new DefaultNamingStrategy(),
            'children',
            FluentEntity::class
        ), function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToMany::class, $relation);
        });

        $this->assertInstanceOf(OneToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_has_one()
    {
        $relation = $this->fluent->hasOne('one', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToOne::class, $relation);
        });

        $this->assertInstanceOf(OneToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_one_to_one()
    {
        $relation = $this->fluent->oneToOne('one', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToOne::class, $relation);
        });

        $this->assertInstanceOf(OneToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_belongs_to()
    {
        $relation = $this->fluent->belongsTo('parent', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToOne::class, $relation);
        });

        $this->assertInstanceOf(ManyToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_many_to_one()
    {
        $relation = $this->fluent->manyToOne('parent', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToOne::class, $relation);
        });

        $this->assertInstanceOf(ManyToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_has_many()
    {
        $relation = $this->fluent->hasMany('children', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToMany::class, $relation);
        });

        $this->assertInstanceOf(OneToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_one_to_many()
    {
        $relation = $this->fluent->oneToMany('children', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToMany::class, $relation);
        });

        $this->assertInstanceOf(OneToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_belongs_to_many()
    {
        $relation = $this->fluent->belongsToMany('children', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToMany::class, $relation);
        });

        $this->assertInstanceOf(ManyToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_many_to_many()
    {
        $relation = $this->fluent->manyToMany('children', FakeEntity::class, function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToMany::class, $relation);
        });

        $this->assertInstanceOf(ManyToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_extend_fluent()
    {
        $this->fluent->macro('timestamps', function (Fluent $builder) {
            $builder->string('createdAt');
        });

        $this->fluent->timestamps();

        foreach ($this->fluent->getQueued() as $field) {
            $field->build();
        }

        $this->assertContains('createdAt', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_can_extend_fluent_with_params()
    {
        $this->fluent->macro('timestamps', function (Fluent $builder, $createdAt, $updatedAt) {
            $builder->string($createdAt);
            $builder->string($updatedAt);
        });

        $this->fluent->timestamps('other_created_field', 'other_updated_field');

        foreach ($this->fluent->getQueued() as $field) {
            $field->build();
        }

        $this->assertContains('other_created_field', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertContains('other_updated_field', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_fluent_should_be_extended_with_closure()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Fluent builder should be extended with a closure argument, none given'
        );

        $this->fluent->macro('fail');
    }

    public function test_fluent_builder_method_should_exist()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Fluent builder method [doesNotExist] does not exist'
        );

        $this->fluent->doesNotExist();
    }
}

class FluentEntity implements Entity
{
    protected $id, $name;
}
