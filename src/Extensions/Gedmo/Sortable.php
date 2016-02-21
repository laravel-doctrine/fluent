<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

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
