<?php

namespace Tests\Relations\Traits;

trait Orderable
{
    public function test_can_order_one_to_many_associations()
    {
        $this->relation->orderBy('id', 'DESC');

        $this->relation->build();

        $this->assertEquals('DESC', $this->getAssocValue($this->field, 'orderBy')['id']);
    }
}
