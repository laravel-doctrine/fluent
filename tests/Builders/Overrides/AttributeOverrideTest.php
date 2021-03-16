<?php

namespace Tests\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\MappingException;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Overrides\AttributeOverride;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;

class AttributeOverrideTest extends TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $this->builder->addField('attribute', 'string');
    }

    public function test_it_should_return_instance_of_field()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The callback should return an instance of LaravelDoctrine\Fluent\Builders\Field');

        $override = $this->override('attribute', function () {
            return 'string';
        });

        $override->build();
    }

    public function test_the_overridden_field_should_exist()
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('No mapping found for field \'non_existing\' on class \'Tests\Stubs\Entities\StubEntity\'.');

        $override = $this->override('non_existing', function () {
        });

        $override->build();
    }

    public function test_can_override_column_name()
    {
        $override = $this->override('attribute', function ($field) {
            return $field->name('overridden_name');
        });

        $override->build();

        $this->assertEquals('overridden_name', $this->builder->getClassMetadata()->getColumnName('attribute'));
    }

    public function test_default_settings_should_be_kept()
    {
        $this->builder->addField('nullable_attribute', 'string', [
            'nullable' => true
        ]);

        $override = $this->override('nullable_attribute', function ($field) {
            return $field->name('overridden_name');
        });

        $override->build();

        $this->assertEquals('overridden_name', $this->builder->getClassMetadata()->getColumnName('nullable_attribute'));
        $this->assertTrue($this->builder->getClassMetadata()->getFieldMapping('nullable_attribute')['nullable']);
    }

    public function test_can_override_settings()
    {
        $this->builder->addField('nullable_attribute', 'string', [
            'nullable' => true
        ]);

        $override = $this->override('nullable_attribute', function ($field) {
            return $field->name('overridden_name')->nullable(false);
        });

        $override->build();

        $this->assertEquals('overridden_name', $this->builder->getClassMetadata()->getColumnName('nullable_attribute'));
        $this->assertFalse($this->builder->getClassMetadata()->getFieldMapping('nullable_attribute')['nullable']);
    }

    public function test_adding_new_options_will_merge_them()
    {
        $this->builder->addField('nullable_attribute', 'string', [
            'options' => [
                'default' => 'some_default'
            ]
        ]);

        $override = $this->override('nullable_attribute', function ($field) {
            return $field->name('overridden_name')->option('some_option', 'some_value');
        });

        $override->build();

        $this->assertEquals('overridden_name', $this->builder->getClassMetadata()->getColumnName('nullable_attribute'));
        $this->assertEquals('some_value', $this->builder->getClassMetadata()->getFieldMapping('nullable_attribute')['options']['some_option']);
        $this->assertEquals('some_default', $this->builder->getClassMetadata()->getFieldMapping('nullable_attribute')['options']['default']);
    }

    protected function override($field, $callback)
    {
        return new AttributeOverride(
            $this->builder,
            new DefaultNamingStrategy(),
            $field,
            $callback
        );
    }
}
