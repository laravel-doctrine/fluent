<?php

namespace Tests\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable;

class SortableTest extends \PHPUnit_Framework_TestCase
{
    public function test_enables_position_and_group()
    {
        Sortable::enable();
    }
}