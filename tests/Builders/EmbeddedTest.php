<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Builders\Embedded;

class EmbeddedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Embedded
     */
    protected $embedded;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEmbeddable::class
        ));

        $this->embedded = new Embedded(
            $this->builder,
            new DefaultNamingStrategy,
            'field',
            FluentEmbeddable::class
        );
    }

    public function test_can_build()
    {
        $this->embedded->build();

        $embedded = $this->builder->getClassMetadata()->embeddedClasses['field'];

        $this->assertEquals(FluentEmbeddable::class, $embedded['class']);
    }

    public function test_default_prefix()
    {
        $this->embedded->build();

        $embedded = $this->builder->getClassMetadata()->embeddedClasses['field'];

        $this->assertNull($embedded['columnPrefix']);
    }

    public function test_can_add_prefix()
    {
        $this->embedded->prefix('prefix_');
        $this->embedded->build();

        $embedded = $this->builder->getClassMetadata()->embeddedClasses['field'];

        $this->assertEquals('prefix_', $embedded['columnPrefix']);
    }

    public function test_can_add_no_prefix()
    {
        $this->embedded->noPrefix();
        $this->embedded->build();

        $embedded = $this->builder->getClassMetadata()->embeddedClasses['field'];

        $this->assertFalse($embedded['columnPrefix']);
    }
}

class FluentEmbeddable
{

}
