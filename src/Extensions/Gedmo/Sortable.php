<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Extensions\Extension;

class Sortable implements Extension
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
