<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Translatable;

use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;

class TranslationMapping extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return Translation::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->table('ext_translations');
        $builder->entity()->setRepositoryClass(TranslationRepository::class);

        $builder->index(['locale', 'object_class', 'foreign_key'])->name('translations_lookup_idx');
        $builder->unique(['locale', 'object_class', 'field', 'foreign_key'])->name('lookup_unique_idx');
    }
}
