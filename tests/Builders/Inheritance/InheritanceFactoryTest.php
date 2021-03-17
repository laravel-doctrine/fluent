<?php

namespace Tests\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Inheritance\Inheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\InheritanceFactory;
use LaravelDoctrine\Fluent\Builders\Inheritance\JoinedTableInheritance;
use LaravelDoctrine\Fluent\Builders\Inheritance\SingleTableInheritance;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Entities\StubEntity;

class InheritanceFactoryTest extends TestCase
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
    }

    public function test_can_create_single_table_inheritance_from_string()
    {
        $this->assertInstanceOf(
            SingleTableInheritance::class,
            InheritanceFactory::create('SINGLE_TABLE', $this->builder)
        );
    }

    public function test_can_create_single_table_inheritance_from_id()
    {
        $this->assertInstanceOf(
            SingleTableInheritance::class,
            InheritanceFactory::create(Inheritance::SINGLE, $this->builder)
        );
    }

    public function test_can_create_joined_table_inheritance_from_string()
    {
        $this->assertInstanceOf(
            JoinedTableInheritance::class,
            InheritanceFactory::create('JOINED', $this->builder)
        );
    }

    public function test_can_create_joined_table_inheritance_from_id()
    {
        $this->assertInstanceOf(
            JoinedTableInheritance::class,
            InheritanceFactory::create(Inheritance::JOINED, $this->builder)
        );
    }

    public function test_can_only_create_joined_or_single_table_inheritance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Inheritance type [NON_EXISTING] does not exist. SINGLE_TABLE and JOINED are support');

        InheritanceFactory::create('NON_EXISTING', $this->builder);
    }
}
