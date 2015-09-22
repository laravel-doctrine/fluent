<?php

namespace Tests\Relations\Traits;

trait Owning
{
    public function test_can_own_a_relation()
    {
        $this->relation->owns('children');

        $this->relation->build();

        $this->assertEquals('children', $this->getAssocValue($this->field, 'inversedBy'));
    }

    public function test_can_be_inversed_by()
    {
        $this->relation->inversedBy('children2');

        $this->relation->build();

        $this->assertEquals('children2', $this->getAssocValue($this->field, 'inversedBy'));
    }
}
