<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
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

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new Tree($this->classMetadata);
    }

    public function test_it_should_add_itself_as_a_builder_macro()
    {
        Tree::enable();

        $builder = (new Builder(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), new DefaultNamingStrategy()
        ));

        $this->assertInstanceOf(
            Tree::class,
            call_user_func([$builder, Tree::MACRO_METHOD])
        );
    }

    public function test_it_should_at_nested_set_as_alias_for_tree()
    {
        Tree::enable();

        $builder = (new Builder(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), new DefaultNamingStrategy()
        ));

        $this->assertInstanceOf(
            Tree::class,
            call_user_func([$builder, 'nestedSet'])
        );

        $this->assertInstanceOf(
            Tree::class,
            call_user_func([$builder, 'nestedSet'], function($tree) {
                $this->assertInstanceOf(Tree::class, $tree);
            })
        );
    }

    public function test_can_mark_entity_as_tree()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'root'             => null,
            'level'            => null,
            'right'            => null,
            'left'             => null,
            'strategy'         => 'nested',
            'activate_locking' => false,
            'locking_timeout'  => 3,
            'closure'          => null
        ]);
    }

    public function test_nested_set_autocompletes_all_fields()
    {
        $this->getNestedSet()->build();

        $this->assertBuildResultIs([
            'root'             => 'root',
            'level'            => 'level',
            'right'            => 'right',
            'left'             => 'left',
            'strategy'         => 'nested',
            'activate_locking' => false,
            'locking_timeout'  => 3,
            'closure'          => null
        ]);
    }

    public function test_nested_set_autocompletes_missing_fields()
    {
        $this->getNestedSet()
             ->left('lft')
             ->right('rgt')
             ->build();

        $this->assertBuildResultIs([
            'root'             => 'root',
            'level'            => 'level',
            'right'            => 'rgt',
            'left'             => 'lft',
            'strategy'         => 'nested',
            'activate_locking' => false,
            'locking_timeout'  => 3,
            'closure'          => null
        ]);
    }

    public function test_can_set_custom_nested_set_columns_on_tree()
    {
        $this->getExtension()
             ->left('lft')
             ->right('rgt')
             ->level('lvl')
             ->root('r')
             ->build();

        $this->assertBuildResultIs([
            'root'             => 'r',
            'level'            => 'lvl',
            'right'            => 'rgt',
            'left'             => 'lft',
            'strategy'         => 'nested',
            'activate_locking' => false,
            'locking_timeout'  => 3,
            'closure'          => null
        ]);
    }

    public function test_can_set_custom_settings_on_tree()
    {
        $this->getExtension()
             ->strategy('materializedPath')
             ->activateLocking()
             ->lockingTimeout(5)
             ->closure('Closure')
             ->build();

        $this->assertBuildResultIs([
            'root'             => null,
            'level'            => null,
            'right'            => null,
            'left'             => null,
            'strategy'         => 'materializedPath',
            'activate_locking' => true,
            'locking_timeout'  => 5,
            'closure'          => 'Closure'
        ]);
    }

    public function test_cannot_use_invalid_tree_strategy()
    {
        $this->setExpectedException(InvalidMappingException::class, 'Tree type: invalid is not available.');

        $this->getExtension()
             ->strategy('invalid')
             ->build();
    }

    public function test_locking_timout_should_be_at_least_one()
    {
        $this->setExpectedException(InvalidMappingException::class,
            'Tree Locking Timeout must be at least of 1 second.');

        $this->getExtension()
             ->lockingTimeout(0)
             ->build();
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
     * @return Tree
     */
    protected function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TreeDriver::EXTENSION_NAME;
    }

    /**
     * @return Tree
     */
    private function getNestedSet()
    {
        return (new Tree($this->classMetadata, 'nested', true));
    }
}
