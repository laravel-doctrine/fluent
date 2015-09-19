<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\Table;
use LaravelDoctrine\Fluent\Entity;
use LogicException;

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

        $this->fluent = Builder::createEntity($this->builder);
    }

    public function test_can_create_entity()
    {
        $entity = Builder::createEntity($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertFalse($entity->getClassMetadata()->isEmbeddedClass);
        $this->assertFalse($entity->getClassMetadata()->isMappedSuperclass);
    }

    public function test_can_create_embeddable()
    {
        $entity = Builder::createEmbeddable($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertTrue($entity->getClassMetadata()->isEmbeddedClass);
    }

    public function test_can_create_superclass()
    {
        $entity = Builder::createMappedSuperClass($this->builder);

        $this->assertInstanceOf(Builder::class, $entity);
        $this->assertFalse($entity->getClassMetadata()->isEmbeddedClass);
        $this->assertTrue($entity->getClassMetadata()->isMappedSuperclass);
    }

    public function test_can_set_table_settings()
    {
        $table = $this->fluent->table('users');

        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals('users', $table->getClassMetadata()->getTableName());

        // Closure usage
        $table = $this->fluent->table(function ($table) {
            $this->assertInstanceOf(Table::class, $table);
        });

        $this->assertInstanceOf(Table::class, $table);
    }

    public function test_cannot_use_table_settings_for_embeddable()
    {
        $fluent = Builder::createEmbeddable($this->builder);

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
        $fluent = Builder::createEmbeddable($this->builder);

        $this->setExpectedException(LogicException::class);

        $fluent->entity();
    }

    public function test_can_create_field()
    {
        $field = $this->fluent->field('integer', 'id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getPendingFields());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_can_add_increments_to_entity()
    {
        $field = $this->fluent->increments('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getPendingFields());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getIdentifier());
        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
    }

    public function test_cannot_add_increments_to_embeddable()
    {
        $fluent = Builder::createEmbeddable($this->builder);

        $this->setExpectedException(LogicException::class);

        $fluent->increments('id');
    }

    public function test_can_add_string()
    {
        $field = $this->fluent->string('name', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getPendingFields());

        $field->getBuilder()->build();

        $this->assertContains('name', $this->fluent->getClassMetadata()->getFieldNames());
    }
}

class FluentEntity implements Entity
{
    protected $id, $name;
}
