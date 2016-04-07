<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Translatable;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\MappedSuperClassMapping;

class AbstractPersonalTranslationMapping extends MappedSuperClassMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return AbstractPersonalTranslation::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->integer('id')->unsigned()->primary()->generatedValue(function (GeneratedValue $builder) {
            $builder->identity();
        });
        $builder->string('locale')->length(8);
        $builder->string('field')->length(32);
        $builder->text('content')->nullable();
    }
}
