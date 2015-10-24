<?php

namespace Tests\Relations\Traits;

trait Primary
{
    public function test_can_make_relation_a_primary_key()
    {
        $this->assertFalse($this->relation->getBuilder()->getClassMetadata()->isIdentifier($this->field));

        $this->relation->primary();
        $this->relation->build();

        $this->assertTrue($this->relation->getBuilder()->getClassMetadata()->isIdentifier($this->field));
    }
}
