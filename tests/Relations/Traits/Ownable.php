<?php

namespace Tests\Relations\Traits;

trait Ownable
{
    public function test_can_be_owned_by()
    {
        $this->relation->ownedBy('parent');

        $this->relation->build();

        $this->assertEquals('parent', $this->getAssocValue($this->field, 'mappedBy'));
    }

    public function test_can_be_mapped_by()
    {
        $this->relation->mappedBy('parent2');

        $this->relation->build();

        $this->assertEquals('parent2', $this->getAssocValue($this->field, 'mappedBy'));
    }
}
