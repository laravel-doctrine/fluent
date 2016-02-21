<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable\SortableGroup;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable\SortablePosition;

class Sortable
{
    /**
     * @return void
     */
    public static function enable()
    {
        SortablePosition::enable();
        SortableGroup::enable();
    }
}
