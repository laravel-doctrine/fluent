<?php

namespace Tests\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable;
use PHPUnit\Framework\TestCase;

class SortableTest extends TestCase
{
    public function test_enables_position_and_group()
    {
        $this->expectNotToPerformAssertions();

        Sortable::enable();
    }
}
