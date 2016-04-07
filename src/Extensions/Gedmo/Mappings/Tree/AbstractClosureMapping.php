<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Mappings\Tree;

use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\MappedSuperClassMapping;

class AbstractClosureMapping extends MappedSuperClassMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return AbstractClosure::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->integer('id')->unsigned()->primary()->generatedValue(function (GeneratedValue $builder) {
            $builder->identity();
        });
        $builder->integer('depth');
    }
}
