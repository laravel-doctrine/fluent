<?php

namespace Tests\Builders\Inheritance;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Inheritance\SingleTableInheritance;
use Tests\Stubs\Entities\StubEntity;

class SingleTableInheritanceTest extends InheritanceTestCase
{
    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var SingleTableInheritance
     */
    protected $inheritance;

    protected function setUp(): void
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            StubEntity::class
        ));

        $this->inheritance = new SingleTableInheritance(
            $this->builder
        );
    }

    public function test_sets_right_type()
    {
        $this->assertTrue($this->builder->getClassMetadata()->isInheritanceTypeSingleTable());
    }
}
