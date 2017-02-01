<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Loggable;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\MappedSuperClassMapping;

class AbstractLogEntryMapping extends MappedSuperClassMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return AbstractLogEntry::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->increments('id');
        $builder->string('action')->length(8);
        $builder->dateTime('loggedAt')->name('logged_at');
        $builder->string('objectId')->name('object_id')->length(64)->nullable();
        $builder->string('objectClass')->name('object_class');
        $builder->integer('version');
        $builder->array('data')->nullable();
        $builder->string('username')->nullable();
    }
}
