<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\Extension;
use LaravelDoctrine\Fluent\Fluent;

class Tree implements Buildable, Extension
{
    const MACRO_METHOD = 'tree';

    /**
     * @var Buildable
     */
    private $strategy;

    /**
     * @var Fluent
     */
    private $builder;

    /**
     * @param Fluent $builder
     */
    public function __construct(Fluent $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Enable extension.
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Fluent $builder, callable $callback = null) {
            $tree = new static($builder);

            if (is_callable($callback)) {
                call_user_func($callback, $tree);
            }

            return $tree;
        });

        NestedSet::enable();
        MaterializedPath::enable();
        ClosureTable::enable();
    }

    /**
     * Sets the tree strategy to NestedSet, returning it for further customization.
     * See {@link https://en.wikipedia.org/wiki/Nested_set_model} for more information on nested set trees.
     *
     * @return NestedSet
     */
    public function asNestedSet()
    {
        return $this->strategy(new NestedSet($this->builder));
    }

    /**
     * Sets the tree strategy to MaterializedPath, returning it for further customization.
     * See {@link https://bojanz.wordpress.com/2014/04/25/storing-hierarchical-data-materialized-path/} for
     * more information on materialized path trees.
     *
     * @return MaterializedPath
     */
    public function asMaterializedPath()
    {
        return $this->strategy(new MaterializedPath($this->builder));
    }

    /**
     * Sets the tree strategy to ClosureTable, returning it for further customization.
     * See {@link https://coderwall.com/p/lixing/closure-tables-for-browsing-trees-in-sql} for more
     * information on closure table trees.
     *
     * @param string $class The class that represents the ClosureTable. You may extend Gedmo's mapped superclass
     *                      for easier usage. {@see Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure}
     *
     * @return ClosureTable
     */
    public function asClosureTable($class)
    {
        return $this->strategy(new ClosureTable($this->builder, $class));
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->strategy->build();
    }

    /**
     * @param Buildable $strategy
     *
     * @return Buildable
     */
    protected function strategy(Buildable $strategy)
    {
        return $this->strategy = $strategy;
    }
}
