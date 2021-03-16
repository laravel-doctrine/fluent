<?php

namespace Tests\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Inheritance\JoinedTableInheritance;
use Tests\Stubs\Entities\StubEntity;

class JoinedTableInheritanceTest extends InheritanceTestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var JoinedTableInheritance
     */
    protected $inheritance;

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $this->inheritance = new JoinedTableInheritance(
            $this->builder
        );
    }

    public function test_sets_right_type()
    {
        $this->assertTrue($this->builder->getClassMetadata()->isInheritanceTypeJoined());
    }
}
