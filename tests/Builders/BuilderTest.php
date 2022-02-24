<?php

namespace Tests\Builders;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\MappingException;
use DoctrineExtensions\Types\CarbonDateTimeType;
use DoctrineExtensions\Types\CarbonDateTimeTzType;
use DoctrineExtensions\Types\CarbonDateType;
use DoctrineExtensions\Types\CarbonTimeType;
use DoctrineExtensions\Types\ZendDateType;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Embedded;
use LaravelDoctrine\Fluent\Builders\EntityListeners;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\Index;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\JoinedTableInheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\SingleTableInheritance;
use LaravelDoctrine\Fluent\Builders\LifecycleEvents;
use LaravelDoctrine\Fluent\Builders\Primary;
use LaravelDoctrine\Fluent\Builders\Table;
use LaravelDoctrine\Fluent\Builders\UniqueConstraint;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;
use LogicException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tests\FakeEntity;
use Tests\Stubs\Embedabbles\StubEmbeddable;
use Tests\Stubs\StubEntityListener;

class BuilderTest extends TestCase
{
    use IsMacroable, MockeryPHPUnitIntegration;

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
        'string'           => Types::STRING,
        'text'             => Types::TEXT,
        'integer'          => Types::INTEGER,
        'smallInteger'     => Types::SMALLINT,
        'bigInteger'       => Types::BIGINT,
        'float'            => Types::FLOAT,
        'decimal'          => Types::DECIMAL,
        'object'           => Types::OBJECT,
        'boolean'          => Types::BOOLEAN,
        'jsonArray'        => Types::JSON,
        'date'             => Types::DATE_MUTABLE,
        'dateTime'         => Types::DATETIME_MUTABLE,
        'dateTimeTz'       => Types::DATETIMETZ_MUTABLE,
        'time'             => Types::TIME_MUTABLE,
        'carbonDateTime'   => 'carbondatetime',
        'carbonDateTimeTz' => 'carbondatetimetz',
        'carbonDate'       => 'carbondate',
        'carbonTime'       => 'carbontime',
        'zendDate'         => 'zenddate',
        'timestamp'        => 'carbondatetime',
        'timestampTz'      => 'carbondatetimetz',
        'binary'           => Types::BINARY,
        'guid'             => Types::GUID,
        'blob'             => Types::BLOB,
        'array'            => Types::ARRAY,
        'simpleArray'      => Types::SIMPLE_ARRAY,
    ];

    public static function setUpBeforeClass(): void
    {
        Type::addType(CarbonDateTimeType::CARBONDATETIME, CarbonDateTimeType::class);
        Type::addType(CarbonDateTimeTzType::CARBONDATETIMETZ, CarbonDateTimeTzType::class);
        Type::addType(CarbonDateType::CARBONDATE, CarbonDateType::class);
        Type::addType(CarbonTimeType::CARBONTIME, CarbonTimeType::class);
        Type::addType(ZendDateType::ZENDDATE, ZendDateType::class);
    }

    protected function setUp(): void
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
        $this->expectException(LogicException::class);

        $this->fluent->table('users');
    }

    public function test_can_set_entity_settings()
    {
        $entity = $this->fluent->entity(function ($entity) {
            $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
        });

        $this->assertInstanceOf(\LaravelDoctrine\Fluent\Builders\Entity::class, $entity);
    }

    public function test_the_related_entity_builder_should_have_the_same_naming_strategy()
    {
        $entity = $this->fluent->entity();

        $this->assertSame(
            $this->fluent->getNamingStrategy(),
            $entity->getNamingStrategy()
        );
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

        $this->expectException(LogicException::class);

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

    public function test_can_add_one_primary_key()
    {
        $primary = $this->fluent->primary('id');

        $this->assertInstanceOf(Primary::class, $primary);
        $this->assertEquals(['id'], $primary->getFields());
        $this->assertContains($primary, $this->fluent->getQueued());
    }

    public function test_can_add_multiple_primary_keys_as_multiple_parameters()
    {
        $primary = $this->fluent->primary('relation1', 'relation2');

        $this->assertInstanceOf(Primary::class, $primary);
        $this->assertEquals(['relation1', 'relation2'], $primary->getFields());
        $this->assertContains($primary, $this->fluent->getQueued());
    }

    public function test_can_add_multiple_primary_keys_as_array()
    {
        $primary = $this->fluent->primary(['relation1', 'relation2']);

        $this->assertInstanceOf(Primary::class, $primary);
        $this->assertEquals(['relation1', 'relation2'], $primary->getFields());
        $this->assertContains($primary, $this->fluent->getQueued());
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
        $this->assertEquals(Types::INTEGER, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
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
        $this->assertEquals(Types::SMALLINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
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
        $this->assertEquals(Types::BIGINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
    }

    public function test_cannot_add_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->expectException(LogicException::class);

        $this->fluent->increments('id');
    }

    public function test_cannot_add_small_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->expectException(LogicException::class);

        $this->fluent->smallIncrements('id');
    }

    public function test_cannot_add_big_increments_to_embeddable()
    {
        $this->builder->setEmbeddable();

        $this->expectException(LogicException::class);

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
        $this->assertEquals(Types::INTEGER, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
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
        $this->assertEquals(Types::SMALLINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
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
        $this->assertEquals(Types::BIGINT, $this->fluent->getClassMetadata()->getFieldMapping('id')['type']);
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
        $relation = $this->fluent->hasOne(FakeEntity::class, 'one', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToOne::class, $relation);
        });

        $this->assertInstanceOf(OneToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_has_one_implies_an_inverse_one_to_one()
    {
        $relation = $this->fluent->hasOne(FakeEntity::class, 'one');

        $relation->build();

        $result = $this->builder->getClassMetadata()->associationMappings['one'];

        $this->assertFalse($result['isOwningSide'],
            "HasOne relation is an inversed one-to-one, but resulted in the owning side."
        );
    }

    public function test_can_add_one_to_one()
    {
        $relation = $this->fluent->oneToOne(FakeEntity::class, 'one', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToOne::class, $relation);
        });

        $this->assertInstanceOf(OneToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_belongs_to()
    {
        $relation = $this->fluent->belongsTo(FakeEntity::class, 'parent', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToOne::class, $relation);
        });

        $this->assertInstanceOf(ManyToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_many_to_one()
    {
        $relation = $this->fluent->manyToOne(FakeEntity::class, 'parent', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToOne::class, $relation);
        });

        $this->assertInstanceOf(ManyToOne::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_has_many()
    {
        $relation = $this->fluent->hasMany(FakeEntity::class, 'children', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToMany::class, $relation);
        });

        $this->assertInstanceOf(OneToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_one_to_many()
    {
        $relation = $this->fluent->oneToMany(FakeEntity::class, 'children', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(OneToMany::class, $relation);
        });

        $this->assertInstanceOf(OneToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_belongs_to_many()
    {
        $relation = $this->fluent->belongsToMany(FakeEntity::class, 'children', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToMany::class, $relation);
        });

        $this->assertInstanceOf(ManyToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_add_many_to_many()
    {
        $relation = $this->fluent->manyToMany(FakeEntity::class, 'children', function ($relation) {
            $this->assertInstanceOf(Relation::class, $relation);
            $this->assertInstanceOf(ManyToMany::class, $relation);
        });

        $this->assertInstanceOf(ManyToMany::class, $relation);
        $this->assertContains($relation, $this->fluent->getQueued());
    }

    public function test_can_embed_embeddables()
    {
        $embedded = $this->fluent->embed(StubEmbeddable::class, 'embedded', function ($embedded) {
            $this->assertInstanceOf(Embedded::class, $embedded);
        });

        $this->assertInstanceOf(Embedded::class, $embedded);
        $this->assertContains($embedded, $this->fluent->getQueued());
    }

    public function test_fluent_builder_method_should_exist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fluent builder method [doesNotExist] does not exist');

        $this->fluent->doesNotExist();
    }

    public function test_events_can_be_associated_to_the_entity()
    {
        $lifecycleEvents = $this->fluent->events();

        $this->assertInstanceOf(LifecycleEvents::class, $lifecycleEvents);
        $this->assertContains($lifecycleEvents, $this->fluent->getQueued());
    }

    public function test_events_can_be_configured_through_a_callable()
    {
        $this->fluent->events(function (LifecycleEvents $events) {
            $events->preFlush('swipeFloor');
            $events->postRemove('cleanToilet');
        });

        foreach ($this->fluent->getQueued() as $buildable) {
            $buildable->build();
        }

        $this->assertTrue(
            $this->fluent->getClassMetadata()->hasLifecycleCallbacks('preFlush')
        );

        $this->assertTrue(
            $this->fluent->getClassMetadata()->hasLifecycleCallbacks('postRemove')
        );
    }

    public function test_can_add_entity_listener()
    {
        $entityListeners = $this->fluent->listen();

        $this->assertInstanceOf(EntityListeners::class, $entityListeners);
        $this->assertContains($entityListeners, $this->fluent->getQueued());
    }

    public function test_entity_listeners_can_be_configured_through_a_callable()
    {
        $this->fluent->listen(function (EntityListeners $events) {
            $events->onFlush(StubEntityListener::class, 'swipeFloor');
            $events->postFlush(StubEntityListener::class, 'cleanToilet');

            // defaults to the event name as method
            $events->onClear(StubEntityListener::class);
        });

        foreach ($this->fluent->getQueued() as $buildable) {
            $buildable->build();
        }

        $this->assertEquals([
            [
                'class'  => StubEntityListener::class,
                'method' => 'swipeFloor',
            ]
        ], $this->fluent->getClassMetadata()->entityListeners['onFlush']);

        $this->assertEquals([
            [
                'class'  => StubEntityListener::class,
                'method' => 'cleanToilet',
            ]
        ], $this->fluent->getClassMetadata()->entityListeners['postFlush']);

        $this->assertEquals([
            [
                'class'  => StubEntityListener::class,
                'method' => 'onClear',
            ]
        ], $this->fluent->getClassMetadata()->entityListeners['onClear']);
    }

    public function test_can_override_an_attribute()
    {
        $this->fluent->string('name');

        $this->fluent->override('name', function ($field) {
            return $field->name('other_name')->nullable();
        });

        $this->fluent->build();

        $this->assertEquals('other_name', $this->fluent->getClassMetadata()->getFieldMapping('name')['columnName']);
        $this->assertTrue($this->fluent->getClassMetadata()->getFieldMapping('name')['nullable']);
    }

    public function test_can_override_many_to_one_association()
    {
        $this->fluent->manyToOne(FluentEntity::class, 'manyToOne');

        $this->fluent->override('manyToOne', function ($relation) {
            return $relation->source('source_id')->target('target_id');
        });

        $this->fluent->build();

        $this->assertEquals('target_id',
            $this->fluent->getClassMetadata()->getAssociationMapping('manyToOne')['joinColumns'][0]['name']);
        $this->assertEquals('source_id', $this->fluent->getClassMetadata()
                                                      ->getAssociationMapping('manyToOne')['joinColumns'][0]['referencedColumnName']);
    }

    public function test_can_override_many_to_many_association()
    {
        $this->fluent->manyToMany(FluentEntity::class, 'manyToMany');

        $this->fluent->override('manyToMany', function ($relation) {
            return $relation->joinTable('custom_table_name')->source('source_id');
        });

        $this->fluent->build();

        $this->assertEquals('custom_table_name',
            $this->fluent->getClassMetadata()->getAssociationMapping('manyToMany')['joinTable']['name']);
        $this->assertEquals('source_id', $this->fluent->getClassMetadata()
                                                      ->getAssociationMapping('manyToMany')['joinTable']['joinColumns'][0]['name']);
    }

    public function test_can_guess_a_one_to_one_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->oneToOne(FluentEntity::class);

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntity');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the oneToOne relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_has_one_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->hasOne(FluentEntity::class);

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntity');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the hasOne relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_belongs_to_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->belongsTo(FluentEntity::class);

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntity');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the belongsTo relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_one_to_many_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->oneToMany(FluentEntity::class)->mappedBy('fluentEntity');

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntities');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the oneToMany relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_has_many_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->hasMany(FluentEntity::class)->mappedBy('fluentEntity');

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntities');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the hasMany relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_many_to_many_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->manyToMany(FluentEntity::class);

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntities');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the manyToMany relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_a_belongs_to_many_relation_name()
    {
        $this->expectNotToPerformAssertions();

        $this->fluent->belongsToMany(FluentEntity::class);

        $this->fluent->build();

        try {
            $this->fluent->getClassMetadata()->getAssociationMapping('fluentEntities');
        } catch (MappingException $e) {
            $this->fail("Could not find default name for the belongsToMany relation. " . $e->getMessage());
        }
    }

    public function test_can_guess_an_embeded_field_name()
    {
        $this->fluent->embed(StubEmbeddable::class);

        $this->fluent->build();

        $this->assertArrayHasKey(
            'stubEmbeddable',
            $this->fluent->getClassMetadata()->embeddedClasses
        );
    }

    public function test_buildable_objects_returned_from_macros_get_queued_and_built()
    {
        Builder::macro('foo', function () {
            /** @var Buildable|\Mockery\Mock $buildable */
            $buildable = \Mockery::mock(Buildable::class);
            $buildable->shouldReceive('build')->once();

            return $buildable;
        });

        $this->fluent->foo();
        $this->fluent->build();
    }

    /**
     * {@inheritdoc}
     */
    protected function getMacroableBuilder()
    {
        return $this->fluent;
    }
}

class FluentEntity
{
    protected $id, $name, $fluentEntity, $fluentEntities;
}
