<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Table;
use Tests\Stubs\Entities\StubEntity;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var Table
     */
    protected $table;

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(StubEntity::class));
        $this->table   = new Table($this->builder);
    }

    public function test_can_set_name()
    {
        $this->table->setName('users');

        $this->assertEquals('users', $this->builder->getClassMetadata()->getTableName());
    }
}
