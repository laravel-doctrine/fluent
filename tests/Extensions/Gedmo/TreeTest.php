<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\ClosureTable;
use LaravelDoctrine\Fluent\Extensions\Gedmo\MaterializedPath;
use LaravelDoctrine\Fluent\Extensions\Gedmo\NestedSet;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Tree;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class TreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var Tree
     */
    private $extension;

    /**
     * @var Builder
     */
    private $builder;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->builder       = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);
        $this->extension     = new Tree($this->builder);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_it_should_add_itself_as_a_builder_macro()
    {
        Tree::enable();

        $this->assertInstanceOf(
            Tree::class,
            call_user_func([$this->builder, Tree::MACRO_METHOD])
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_it_should_add_itself_as_a_builder_macro_with_optional_callback()
    {
        Tree::enable();

        $mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once();

        $this->builder->tree(function(Tree $tree) use ($mock) {
            $mock->callMe();
        });
    }

    public function test_it_delegates_on_a_nested_set_buildable()
    {
    	$nested = $this->extension->asNestedSet();

        $this->assertInstanceOf(NestedSet::class, $nested);
    }

    public function test_it_builds_the_delegated_nested_set_on_build()
    {
    	$this->extension->asNestedSet();
        $this->extension->build();

        $this->assertEquals('nested', $this->classMetadata->getExtension($this->getExtensionName())['strategy']);
    }

    public function test_it_delegates_on_a_materialized_path_buildable()
    {
    	$materializedPath = $this->extension->asMaterializedPath();

        $this->assertInstanceOf(MaterializedPath::class, $materializedPath);
    }

    public function test_it_builds_the_delegated_materialized_path_on_build()
    {
    	$this->extension->asMaterializedPath();
        $this->extension->build();

        $this->assertEquals('materializedPath', $this->classMetadata->getExtension($this->getExtensionName())['strategy']);
    }

    public function test_it_delegates_on_a_closure_table_buildable()
    {
    	$materializedPath = $this->extension->asClosureTable("Foo");

        $this->assertInstanceOf(ClosureTable::class, $materializedPath);
    }

    public function test_it_builds_the_delegated_closure_table_on_build()
    {
    	$this->extension->asClosureTable("Foo");
        $this->extension->build();

        $this->assertEquals('closure', $this->classMetadata->getExtension($this->getExtensionName())['strategy']);
    }


    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertBuildResultIs(array $expected)
    {
        $this->assertEquals($expected, $this->classMetadata->getExtension(
            $this->getExtensionName()
        ));
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TreeDriver::EXTENSION_NAME;
    }
}
