<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Loggable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Versioned;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use PHPUnit_Framework_TestCase;

class VersionedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Versioned
     */
    private $builder;

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $fieldName;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->fieldName     = 'someField';

        $this->builder = new Versioned($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_versioned_as_a_field_macro()
    {
        Versioned::enable();

        $field = Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'foo');

        $field->versioned();
        $field->build();

        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_should_add_versioned_as_a_many_to_one_macro()
    {
        Versioned::enable();

        $relation = new ManyToOne(
            new ClassMetadataBuilder($this->classMetadata),
            new DefaultNamingStrategy(),
            'someRelation',
            'SomeEntity'
        );


        $relation->versioned();
        $relation->build();

        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_should_add_versioned_as_a_one_to_one_macro()
    {
        Versioned::enable();

        $relation = new OneToOne(
            new ClassMetadataBuilder($this->classMetadata),
            new DefaultNamingStrategy(),
            'someRelation',
            'SomeEntity'
        );


        $relation->versioned();
        $relation->build();

        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_should_mark_the_field_as_versioned()
    {
        $this->builder->build();

        $this->assertEquals([
            'loggable'  => true,
            'versioned' => [$this->fieldName],
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_shouldnt_override_previous_fields()
    {
        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, [
            'loggable'  => true,
            'versioned' => ['bar', 'baz'],
        ]);

        $this->builder->build();

        $this->assertEquals([
            'loggable'  => true,
            'versioned' => ['bar', 'baz', $this->fieldName],
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_should_not_duplicate_fields()
    {
        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, [
            'loggable'  => true,
            'versioned' => [$this->fieldName],
        ]);

        $this->builder->build();

        $this->assertEquals([
            'loggable'  => true,
            'versioned' => [$this->fieldName],
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
}
