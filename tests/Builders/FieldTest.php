<?php

namespace Tests\Builders;

use BadMethodCallException;
use Doctrine\DBAL\Types\StringType;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;

class FieldTest extends TestCase
{
    use IsMacroable, MockeryPHPUnitIntegration;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Field
     */
    protected $field;

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $this->builder->setTable('stub_entities');

        $this->field = Field::make($this->builder, 'string', 'name');
    }

    public function test_can_make_new_field()
    {
        $this->assertInstanceOf(Field::class, $this->field);
    }

    public function test_can_make_field_nullable()
    {
        $this->field->nullable();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->getFieldMapping('name')['nullable']);
    }

    public function test_can_make_field_index()
    {
        $this->field->index();

        $this->field->build();

        $indexes = $this->builder->getClassMetadata()->table['indexes'];

        $this->assertArrayHasKey('stub_entities_name_index', $indexes);
        $this->assertCount(1, $indexes['stub_entities_name_index']['columns']);
        $this->assertContains('name', $indexes['stub_entities_name_index']['columns']);
    }

    public function test_can_make_field_index_with_custom_name()
    {
        $this->field->index('index_name');

        $this->field->build();

        $indexes = $this->builder->getClassMetadata()->table['indexes'];

        $this->assertArrayHasKey('index_name', $indexes);
        $this->assertCount(1, $indexes['index_name']['columns']);
        $this->assertContains('name', $indexes['index_name']['columns']);
    }

    public function test_can_set_column_name()
    {
        $this->field->columnName('name_column');

        $this->field->build();

        $this->assertEquals('name_column', $this->builder->getClassMetadata()->getFieldMapping('name')['columnName']);
    }

    public function test_can_set_name()
    {
        $this->field->name('name_column');

        $this->field->build();

        $this->assertEquals('name_column', $this->builder->getClassMetadata()->getFieldMapping('name')['columnName']);
    }

    public function test_can_make_field_auto_increment()
    {
        $this->field->autoIncrement();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->usesIdGenerator());
    }

    public function test_can_set_generated_value_strategy()
    {
        $this->field->generatedValue();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->generatorType == ClassMetadataInfo::GENERATOR_TYPE_AUTO);
    }

    public function test_can_enrich_generated_value_with_a_closure()
    {
        $field = Field::make($this->builder, 'integer', 'gen');

        $field->generatedValue(function (GeneratedValue $builder) {
            $builder->sequence('sequence_name', 4, 15);
        });

        $field->build();

        $this->assertEquals([
            'sequenceName'   => 'sequence_name',
            'initialValue'   => 4,
            'allocationSize' => 15,
        ], $this->builder->getClassMetadata()->sequenceGeneratorDefinition);
    }

    public function test_can_use_a_single_parameter_on_generated_values()
    {
        $field = Field::make($this->builder, 'integer', 'gen');

        $field->generatedValue(function (GeneratedValue $builder) {
            $builder->identity();
        });

        $field->build();

        $this->assertTrue($this->builder->getClassMetadata()->isIdGeneratorIdentity());

        $this->assertNull($this->builder->getClassMetadata()->sequenceGeneratorDefinition);
    }

    public function test_can_make_field_unsigned()
    {
        $this->field->unsigned();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->getFieldMapping('name')['options']['unsigned']);
    }

    public function test_can_set_default_fluently()
    {
        $this->field->default('default');

        $this->field->build();

        $this->assertEquals('default',
            $this->builder->getClassMetadata()->getFieldMapping('name')['options']['default']);
    }

    public function test_can_set_fixed()
    {
        $this->field->fixed('fixed');

        $this->field->build();

        $this->assertEquals('fixed', $this->builder->getClassMetadata()->getFieldMapping('name')['options']['fixed']);
    }

    public function test_can_set_comment()
    {
        $this->field->comment('comment');

        $this->field->build();

        $this->assertEquals('comment',
            $this->builder->getClassMetadata()->getFieldMapping('name')['options']['comment']);
    }

    public function test_can_set_collation()
    {
        $this->field->collation('collation');

        $this->field->build();

        $this->assertEquals('collation',
            $this->builder->getClassMetadata()->getFieldMapping('name')['options']['collation']);
    }

    public function test_can_make_field_primary()
    {
        $this->field->primary();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->getFieldMapping('name')['id']);
    }

    public function test_cannot_call_non_existing_field_builder_methods()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('FieldBuilder method [nonExisting] does not exist.');

        $this->field->nonExisting();
    }

    public function test_versioning_is_fluent()
    {
        $this->assertEquals($this->field, $this->field->useForVersioning());
    }

    public function test_integer_can_be_used_for_versioning()
    {
        $this->doTestValidTypeForVersioning("integer");
    }

    public function test_bigint_can_be_used_for_versioning()
    {
        $this->doTestValidTypeForVersioning("bigint");
    }

    public function test_smallint_can_be_used_for_versioning()
    {
        $this->doTestValidTypeForVersioning("smallint");
    }

    public function test_datetime_can_be_used_for_versioning()
    {
        $this->doTestValidTypeForVersioning("datetime");
    }

    public function test_array_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("array");
    }

    public function test_simple_array_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("simple_array");
    }

    public function test_json_array_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("json");
    }

    public function test_boolean_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("boolean");
    }

    public function test_datetimetz_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("datetimetz");
    }

    public function test_date_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("date");
    }

    public function test_time_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("time");
    }

    public function test_decimal_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("decimal");
    }

    public function test_object_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("object");
    }

    public function test_string_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("string");
    }

    public function test_text_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("text");
    }

    public function test_binary_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("binary");
    }

    public function test_blob_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("blob");
    }

    public function test_float_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("float");
    }

    public function test_guid_cannot_be_used_for_versioning()
    {
        $this->doTestInvalidTypeForVersioning("guid");
    }

    public function test_ids_cannot_be_used_for_versioning()
    {
        $this->expectException(MappingException::class);

        $this->field
            ->primary()
            ->useForVersioning()
            ->build();
    }

    public function test_can_obtain_its_type_after_creation()
    {
        $this->assertInstanceOf(StringType::class, $this->field->getType());
    }

    public function test_buildable_objects_returned_from_macros_get_queued_and_built()
    {
        Field::macro('foo', function(){
            /** @var Buildable|\Mockery\Mock $buildable */
            $buildable = \Mockery::mock(Buildable::class);
            $buildable->shouldReceive('build')->once();

            return $buildable;
        });

        $this->field->foo();
        $this->field->build();
    }

    private function doTestValidTypeForVersioning($type)
    {
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $field   = Field::make($builder, $type, "{$type}Field");

        $field->useForVersioning()->build();

        $cm = $builder->getClassMetadata();
        $this->assertTrue($cm->isVersioned, "Field {$type}Field is not versioned.");
        $this->assertEquals("{$type}Field", $cm->versionField);
    }

    private function doTestInvalidTypeForVersioning($type)
    {
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $field   = Field::make($builder, $type, "aField");

        $this->expectException(MappingException::class);
        $field->useForVersioning()->build();
    }

    /**
     * Get the builder under test.
     *
     * @return Macroable
     */
    protected function getMacroableBuilder()
    {
        return $this->field;
    }
}
