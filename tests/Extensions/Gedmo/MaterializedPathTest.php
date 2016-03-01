<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\MaterializedPath;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Tree;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreePath;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreePathHash;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreePathSource;
use LaravelDoctrine\Fluent\Fluent;
use PHPUnit_Framework_TestCase;

class MaterializedPathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var Fluent
     */
    private $builder;

    /**
     * @var MaterializedPath
     */
    private $tree;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata("Foo");
        $this->builder       = new Builder(new ClassMetadataBuilder($this->classMetadata));
        $this->tree          = new MaterializedPath($this->builder);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_it_should_enable_field_macros()
    {
        MaterializedPath::enable();

        /** @var \Mockery\Mock $mock */
        $mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once();

        $field = $this->builder->integer('foo');
        $this->assertInstanceOf(TreePath::class, $field->treePath('|', function ($path) use ($mock) {
            $mock->callMe();
        }));

        $this->assertInstanceOf(TreePathHash::class, $field->treePathHash());
        $this->assertInstanceOf(TreePathSource::class, $field->treePathSource());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function test_it_gets_along_with_other_tree_field_builders()
    {
    	Tree::enable();

        $this->builder->tree()->asMaterializedPath();

        $this->builder->belongsTo('Foo', 'before')->treeParent();
        $this->builder->bigInteger('height')->treeLevel();
        $this->builder->string('title')->treePathSource();
        $this->builder->string('hash')->treePathHash();
        $this->builder->string('breadcrumb')->treePath('/')
                ->appendId()
                ->startsWithSeparator()
                ->endsWithSeparator(false);

        $this->builder->build();

        $this->assertExtensionEquals([
            'strategy'                   => 'materializedPath',
            'path'                       => 'breadcrumb',
            'parent'                     => 'before',
            'level'                      => 'height',
            'path_source'                => 'title',
            'path_hash'                  => 'hash',
            'path_separator'             => '/',
            'path_append_id'             => true,
            'path_starts_with_separator' => true,
            'path_ends_with_separator'   => false,
            'activate_locking'           => false,
        ]);
    }


    public function test_it_lets_me_customize_the_path_field()
    {
        $this->tree->path('thePath')->build();

        $this->assertExtensionKeyEquals('path', 'thePath');
    }

    public function test_the_path_can_have_custom_separators()
    {
        $this->tree->path('thePath', '@')->build();

        $this->assertExtensionKeyEquals('path_separator', '@');
    }

    public function test_it_lets_me_customize_the_path_hash_field()
    {
        $this->tree->pathHash('thePathHash')->build();

        $this->assertExtensionKeyEquals('path_hash', 'thePathHash');
    }

    public function test_setting_the_path_creates_the_field_for_it()
    {
        $this->tree->path('somePath')->build();
        $this->builder->build();

        $this->assertEquals('string', $this->classMetadata->getFieldMapping('somePath')['type']);
    }

    public function test_it_has_a_parent_reference()
    {
        $this->tree->parent('ancestor')->build();

        $this->assertExtensionKeyEquals('parent', 'ancestor');
    }

    public function test_it_has_a_field_to_act_as_source_for_the_path()
    {
        $this->tree->pathSource('title')->build();

        $this->assertExtensionKeyEquals('path_source', 'title');
    }

    public function test_has_defaults_for_everything()
    {
        $this->tree->build();

        $this->assertExtensionEquals([
            'strategy'                   => 'materializedPath',
            'path'                       => 'path',
            'parent'                     => 'parent',
            'path_source'                => 'id',
            'path_separator'             => '|',
            'path_append_id'             => null,
            'path_starts_with_separator' => false,
            'path_ends_with_separator'   => true,
            'activate_locking'           => false,
        ]);
    }

    public function test_it_returns_a_false_value_for_activate_locking_as_its_not_supported()
    {
    	$this->tree->build();

        $this->assertExtensionKeyEquals('activate_locking', false);
    }


    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertExtensionEquals(array $expected)
    {
        $this->assertEquals($expected, $this->getExtension());
    }

    /**
     * Assert that a given key of the built extension matches the expected value.
     *
     * @param string $key
     * @param mixed  $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertExtensionKeyEquals($key, $expected)
    {
        $extension = $this->getExtension();

        $this->assertArrayHasKey($key, $extension, "Extension does not have key [$key].");
        $this->assertEquals($expected, $extension[$key]);
    }

    /**
     * @return array
     */
    protected function getExtension()
    {
        return $this->classMetadata->getExtension(TreeDriver::EXTENSION_NAME);
    }
}
