<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Loggable;

use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;

class LogEntryMapping extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return LogEntry::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->table('ext_log_entries');
        $builder->entity()->setRepositoryClass(LogEntryRepository::class);

        $builder->index(['object_class'])->name('log_class_lookup_idx');
        $builder->index(['logged_at'])->name('log_date_lookup_idx');
        $builder->index(['username'])->name('log_user_lookup_idx');
        $builder->index(['object_id', 'object_class', 'version'])->name('log_version_lookup_idx');
    }
}
