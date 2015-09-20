<?php

namespace Tests\Relations\Traits;

trait Indexable
{
    public function test_can_set_index()
    {
        $this->relation->indexBy('index_column');

        $this->relation->build();

        $this->assertEquals('index_column', $this->getAssocValue($this->field, 'indexBy'));
    }
}
