<?php

namespace Tests\Relations\Traits;

trait OneTo
{
    public function test_can_call_magic_one_to_many_assoc_methods()
    {
        $this->relation->orphanRemoval('parent_id');

        $this->relation->build();

        $this->assertTrue($this->getAssocValue($this->field, 'orphanRemoval'));
    }
}
