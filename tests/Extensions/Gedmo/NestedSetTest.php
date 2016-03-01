<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\Gedmo\NestedSet;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Tree;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeStrategy;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class NestedSetTest extends TreeStrategyTest
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var NestedSet
     */
    protected $strategy;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_building_a_nested_tree_through_the_tree_facade()
    {
        Tree::enable();

        $this->builder->tree()->asNestedSet()->left('izq')->right('der')->parent('padre');
        $this->builder->build();

        $this->assertExtensionEquals([
            'strategy' => 'nested',
            'left'     => 'izq',
            'right'    => 'der',
            'root'     => null,
            'parent'   => 'padre',
        ]);
    }

    public function test_it_gets_along_with_the_other_field_macros()
    {
        Tree::enable();

        $this->builder->tree()->asNestedSet();

        $this->builder->integer('left')->treeLeft();
        $this->builder->integer('right')->treeRight();
        $this->builder->integer('level')->treeLevel();
        $this->builder->belongsTo('Foo', 'parent')->treeParent();
        $this->builder->belongsTo('Foo', 'root')->treeRoot();
        $this->builder->build();

        $this->assertExtensionEquals([
            'strategy' => 'nested',
            'left'     => 'left',
            'right'    => 'right',
            'level'    => 'level',
            'root'     => 'root',
            'parent'   => 'parent',
        ]);
    }

    public function test_can_mark_entity_as_a_nested_set_tree()
    {
        $this->strategy->build();

        $this->assertExtensionKeyEquals('strategy', 'nested');
    }

    public function test_it_has_defaults_for_all_required_fields()
    {
        $this->strategy->build();

        $this->assertExtensionEquals([
            'strategy' => 'nested',
            'left'     => 'left',
            'right'    => 'right',
            'root'     => null,
            'parent'   => 'parent',
        ]);
    }

    public function test_it_auto_completes_missing_required_fields()
    {
        $this->strategy->left('lft')->right('rgt')->build();

        $this->assertExtensionEquals([
            'strategy' => 'nested',
            'left'     => 'lft',
            'right'    => 'rgt',
            'root'     => null,
            'parent'   => 'parent',
        ]);
    }

    public function test_it_should_create_a_belongs_to_relation_to_the_root_class_on_the_given_field()
    {
        $this->strategy->root('granpa')->build();
        $this->builder->build();

        $this->assertExtensionKeyEquals('root', 'granpa');
        $this->assertArrayHasKey('granpa', $this->classMetadata->associationMappings);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $this->classMetadata->associationMappings['granpa']['type']);
    }

    public function test_it_sets_up_gedmos_repository()
    {
        $this->strategy->build();

        $this->assertEquals(NestedTreeRepository::class, $this->builder->getClassMetadata()->customRepositoryClassName);
    }

    public function getNumericFields()
    {
        return array_merge(parent::getNumericFields(), [
            ['left'],
            ['right'],
        ]);
    }

    public function getRelationFields()
    {
        return array_merge(parent::getRelationFields(), [['root']]);
    }

    public function getAllFields()
    {
        return array_merge($this->getNumericFields(), $this->getRelationFields());
    }

    /**
     * @param Fluent $builder
     *
     * @return TreeStrategy
     */
    protected function getStrategy(Fluent $builder)
    {
        return new NestedSet($builder);
    }
}
