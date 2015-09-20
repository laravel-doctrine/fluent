<?php

namespace Tests\Builders;

use BadMethodCallException;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Field;
use Tests\Stubs\Entities\StubEntity;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Field
     */
    protected $field;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));

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
        $this->field->generatedValue('UUID');

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->isIdentifierUuid());
    }

    public function test_can_make_field_unsigned()
    {
        $this->field->unsigned();

        $this->field->build();

        $this->assertTrue($this->builder->getClassMetadata()->getFieldMapping('name')['options']['unsigned']);
    }

    public function test_can_set_default()
    {
        $this->field->setDefault('default');

        $this->field->build();

        $this->assertEquals('default',
            $this->builder->getClassMetadata()->getFieldMapping('name')['options']['default']);
    }

    public function test_can_set_default_fluently()
    {
        $this->field->default('default2');

        $this->field->build();

        $this->assertEquals('default2',
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
        $this->setExpectedException(
            BadMethodCallException::class,
            'FieldBuilder method [nonExisting] does not exist.'
        );

        $this->field->nonExisting();
    }
}
