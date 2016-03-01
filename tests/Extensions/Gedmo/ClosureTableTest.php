<?php

namespace Tests\Extensions\Gedmo;

use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;
use LaravelDoctrine\Fluent\Extensions\Gedmo\ClosureTable;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Tree;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeStrategy;
use LaravelDoctrine\Fluent\Fluent;

class ClosureTableTest extends TreeStrategyTest
{
    /**
     * @var ClosureTable
     */
    protected $strategy;

    public function test_it_needs_a_related_entity_that_represents_the_closure_table()
    {
        $this->strategy->build();

        $this->assertExtensionEquals([
            'strategy' => 'closure',
            'closure'  => 'Bar',
        ]);
    }

    public function test_the_related_entity_can_be_customized_on_construction()
    {
        $this->getStrategy($this->builder, 'Baz')->build();

        $this->assertExtensionKeyEquals('closure', 'Baz');
    }

    public function test_it_gets_along_well_with_the_field_builder_macros()
    {
        Tree::enable();

    	$this->builder->tree()->asClosureTable('Bar');

        $this->builder->belongsTo('Foo', 'parent')->treeParent();
        $this->builder->integer('lvl')->treeLevel();

        $this->builder->build();

        $this->assertExtensionEquals([
            'strategy' => 'closure',
            'closure'  => 'Bar',
            'parent'   => 'parent',
            'level'    => 'lvl',
        ]);
    }

    public function test_it_adds_the_closure_table_repository_as_default()
    {
    	$this->builder->tree()->asClosureTable('Bar');
        $this->builder->build();

        $this->assertEquals(
            ClosureTreeRepository::class,
            $this->classMetadata->customRepositoryClassName
        );
    }

    /**
     * @param Fluent $builder
     * @param string $class
     *
     * @return TreeStrategy
     */
    protected function getStrategy(Fluent $builder, $class = 'Bar')
    {
        return new ClosureTable($builder, $class);
    }
}
