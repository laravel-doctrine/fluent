<?php

namespace Tests\Builders;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Embedded;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\Index;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\JoinedTableInheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\SingleTableInheritance;
use LaravelDoctrine\Fluent\Builders\LifecycleEvents;
use LaravelDoctrine\Fluent\Builders\Table;
use LaravelDoctrine\Fluent\Builders\UniqueConstraint;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;
use LogicException;
use Tests\FakeEntity;
use Tests\Stubs\Embedabbles\StubEmbeddable;

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

    /**
     * @var array
     */
    protected $types = [
        'string' => Type::STRING,
        'text' => Type::TEXT,
        'integer' => Type::INTEGER,
        'smallInteger' => Type::SMALLINT,
        'bigInteger' => Type::BIGINT,
        'float' => Type::FLOAT,
        'decimal' => Type::DECIMAL,
        'object' => Type::OBJECT,
        'boolean' => Type::BOOLEAN,
        'jsonArray' => Type::JSON_ARRAY,
        'date' => Type::DATE,
        'dateTime' => Type::DATETIME,
        'dateTimeTz' => Type::DATETIMETZ,
        'time' => Type::TIME,
        'timestamp' => Type::DATETIME,
        'timestampTz' => Type::DATETIMETZ,
        'binary' => Type::BINARY,
        'guid' => Type::GUID,
        'blob' => Type::BLOB,
        'array' => Type::TARRAY,
        'setArray' => Type::TARRAY,
        'simpleArray' => Type::SIMPLE_ARRAY,
    ];

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        $this->fluent = new Builder($this->builder);
    }

    public function test_can_get_builder()
    {
        $entity = new Builder($this->builder);

        $this->assertInstanceOf(ClassMetadataBuilder::class, $entity->getBuilder());
    }

    public function test_can_create_entity()
    {
        $this->assertInstanceOf(Builder::class, $this->fluent);
        $this->assertFalse($this->fluent->getClassMetadata()->isEmbeddedClass);
        $this->assertFalse($this->fluent->getClassMetadata()->isMappedSuperclass);
    }

    public function test_can_create_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->assertTrue($this->fluent->getClassMetadata()->isEmbeddedClass);
    }

    public function test_can_create_superclass()
    {
        $this->builder->setMappedSuperClass();

        $this->assertFalse($this->fluent->getClassMetadata()->isEmbeddedClass);
        $this->assertTrue($this->fluent->getClassMetadata()->isMappedSuperclass);
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
        $this->builder->setEmbeddable();
        $this->setExpectedException(LogicException::class);

        $this->fluent->table('users');
    }

    public function test_can_set_entity_settings()
    {
        $entity = $this->fluent->entity(function ($entity) {
            $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
        });

        $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
    }

    public function test_can_set_inheritance()
    {
        $inheritance = $this->fluent->inheritance(Inheritance::SINGLE, function ($inheritance) {
            $this->assertInstanceOf(SingleTableInheritance::class, $inheritance);
            $this->assertInstanceOf(Inheritance::class, $inheritance);
        });

        $this->assertInstanceOf(SingleTableInheritance::class, $inheritance);
        $this->assertInstanceOf(Inheritance::class, $inheritance);
    }

    public function test_can_set_single_table_inheritance()
    {
        $inheritance = $this->fluent->singleTableInheritance(function ($inheritance) {
            $this->assertInstanceOf(SingleTableInheritance::class, $inheritance);
            $this->assertInstanceOf(Inheritance::class, $inheritance);
        });

        $this->assertInstanceOf(SingleTableInheritance::class, $inheritance);
        $this->assertInstanceOf(Inheritance::class, $inheritance);
    }

    public function test_can_set_joined_table_inheritance()
    {
        $inheritance = $this->fluent->joinedTableInheritance(function ($inheritance) {
            $this->assertInstanceOf(JoinedTableInheritance::class, $inheritance);
            $this->assertInstanceOf(Inheritance::class, $inheritance);
        });

        $this->assertInstanceOf(JoinedTableInheritance::class, $inheritance);
        $this->assertInstanceOf(Inheritance::class, $inheritance);
    }

    public function test_cannot_use_entity_settings_for_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->setExpectedException(LogicException::class);

        $this->fluent->entity();
    }

    public function test_can_add_one_column_index()
    {
        $index = $this->fluent->index('name');

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals(['name'], $index->getColumns());
        $this->assertContains($index, $this->fluent->getQueued());
    }

    public function test_can_add_multiple_column_index_as_multiple_parameters()
    {
        $index = $this->fluent->index('name', 'address', 'email');

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals(['name', 'address', 'email'], $index->getColumns());
        $this->assertContains($index, $this->fluent->getQueued());
    }

    public function test_can_add_multiple_column_index_as_array()
    {
        $index = $this->fluent->index(['name', 'address', 'email']);

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals(['name', 'address', 'email'], $index->getColumns());
        $this->assertContains($index, $this->fluent->getQueued());
    }

    public function test_can_add_unique_constraint_for_one_column()
    {
        $unique = $this->fluent->unique('name');

        $this->assertInstanceOf(UniqueConstraint::class, $unique);
        $this->assertEquals(['name'], $unique->getColumns());
        $this->assertContains($unique, $this->fluent->getQueued());
    }

    public function test_can_add_unique_constraint_for_multiple_columns_as_multiple_parameters()
    {
        $unique = $this->fluent->unique('name', 'address', 'email');

        $this->assertInstanceOf(UniqueConstraint::class, $unique);
        $this->assertEquals(['name', 'address', 'email'], $unique->getColumns());
        $this->assertContains($unique, $this->fluent->getQueued());
    }

    public function test_can_add_unique_constraint_for_multiple_column_as_array()
    {
        $unique = $this->fluent->unique(['name', 'address', 'email']);

        $this->assertInstanceOf(UniqueConstraint::class, $unique);
        $this->assertEquals(['name', 'address', 'email'], $unique->getColumns());
        $this->assertContains($unique, $this->fluent->getQueued());
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
        $this->assertEquals(Type::INTEGER, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
    }

    public function test_can_add_small_increments_to_entity()
    {
        $field = $this->fluent->smallIncrements('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getIdentifier());
        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals(Type::SMALLINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
    }

    public function test_can_add_big_increments_to_entity()
    {
        $field = $this->fluent->bigIncrements('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getIdentifier());
        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals(Type::BIGINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
    }

    public function test_cannot_add_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->setExpectedException(LogicException::class);

        $this->fluent->increments('id');
    }

    public function test_cannot_add_small_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->setExpectedException(LogicException::class);

        $this->fluent->smallIncrements('id');
    }

    public function test_cannot_add_big_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->setExpectedException(LogicException::class);

        $this->fluent->bigIncrements('id');
    }

    public function test_can_add_all_fields()
    {
        foreach ($this->types as $method => $type) {
            $field = $this->fluent->{$method}($method, function ($field) {
                $this->assertInstanceOf(Field::class, $field);
            });

            $this->assertInstanceOf(Field::class, $field);
            $this->assertContains($field, $this->fluent->getQueued());

            $field->getBuilder()->build();

            $this->assertContains($method, $this->fluent->getClassMetadata()->getFieldNames());
            $this->assertEquals($type, $this->fluent->getClassMetadata()->getFieldMapping($method)['type']);
        }
    }

    public function test_all_fields_can_be_nullable()
    {
        foreach ($this->types as $method => $type) {
            $field = $this->fluent->{$method}($method)->nullable();

            $this->assertInstanceOf(Field::class, $field);
            $this->assertContains($field, $this->fluent->getQueued());

            $field->getBuilder()->build();

            $this->assertContains($method, $this->fluent->getClassMetadata()->getFieldNames());
            $this->assertEquals($type, $this->fluent->getClassMetadata()->getFieldMapping($method)['type']);
            $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping($method)['nullable']);
        }
    }

    public function test_all_fields_can_be_unique()
    {
        foreach ($this->types as $method => $type) {
            $field = $this->fluent->{$method}($method)->unique();

            $this->assertInstanceOf(Field::class, $field);
            $this->assertContains($field, $this->fluent->getQueued());

            $field->getBuilder()->build();

            $this->assertContains($method, $this->fluent->getClassMetadata()->getFieldNames());
            $this->assertEquals($type, $this->fluent->getClassMetadata()->getFieldMapping($method)['type']);
            $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping($method)['unique']);
        }
    }

    public function test_can_add_unsigned_integer_to_entity()
    {
        $field = $this->fluent->unsignedInteger('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals(Type::INTEGER, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
        $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping('id')['options']['unsigned']);
    }

    public function test_can_add_unsigned_small_integer_to_entity()
    {
        $field = $this->fluent->unsignedSmallInteger('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals(Type::SMALLINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
        $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping('id')['options']['unsigned']);
    }

    public function test_can_add_unsigned_big_integer_to_entity()
    {
        $field = $this->fluent->unsignedBigInteger('id', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('id', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals(Type::BIGINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
        $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping('id')['options']['unsigned']);
    }

    public function test_can_add_remember_token()
    {
        $field = $this->fluent->rememberToken('rememberToken', function ($field) {
            $this->assertInstanceOf(Field::class, $field);
        });

        $this->assertInstanceOf(Field::class, $field);
        $this->assertContains($field, $this->fluent->getQueued());

        $field->getBuilder()->build();

        $this->assertContains('rememberToken', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertEquals('string', $this->fluent->getClassMetadata()->getFieldMapping('rememberToken')['type']);
        $this->assertEquals(100, $this->fluent->getClassMetadata()->getFieldMapping('rememberToken')['length']);
        $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping('rememberToken')['nullable']);
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

    public function test_can_embed_embeddables()
    {
        $embedded = $this->fluent->embed('embedded', StubEmbeddable::class, function ($embedded) {
            $this->assertInstanceOf(Embedded::class, $embedded);
        });

        $this->assertInstanceOf(Embedded::class, $embedded);
        $this->assertContains($embedded, $this->fluent->getQueued());
    }

    public function test_can_extend_fluent()
    {
        Builder::macro('timestamps', function (Fluent $builder) {
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
        Builder::macro('timestamps', function (Fluent $builder, $createdAt, $updatedAt) {
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

    public function test_two_different_instances_of_fluent_contain_all_macros()
    {
        Builder::macro('aMacro', function (Fluent $builder) {
            $builder->string('aField');
        });

        $this->fluent->aMacro();

        Builder::macro('anotherMacro', function (Fluent $builder) {
            $builder->string('anotherField');
        });

        $this->fluent->anotherMacro();

        $fluent = new Builder(new ClassMetadataBuilder(new ClassMetadataInfo(FluentEntity::class)));

        $fluent->aMacro();
        $fluent->anotherMacro();

        foreach ($this->fluent->getQueued() as $field) {
            $field->build();
        }

        foreach ($fluent->getQueued() as $field) {
            $field->build();
        }

        $this->assertContains('aField', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertContains('anotherField', $this->fluent->getClassMetadata()->getFieldNames());
        $this->assertContains('aField', $fluent->getClassMetadata()->getFieldNames());
        $this->assertContains('anotherField', $fluent->getClassMetadata()->getFieldNames());
    }

    public function test_events_can_be_associated_to_the_entity()
    {
        $lifecycleEvents = $this->fluent->events();

        $this->assertInstanceOf(LifecycleEvents::class, $lifecycleEvents);
        $this->assertContains($lifecycleEvents, $this->fluent->getQueued());
    }
}

class FluentEntity
{
    protected $id, $name;
}
