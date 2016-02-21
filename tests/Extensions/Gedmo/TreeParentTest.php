<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeParent;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class TreeParentTest extends \PHPUnit_Framework_TestCase
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
     * @var TreeParent
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'parent';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();

        $this->extension = new TreeParent($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        TreeParent::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreeParent::class,
            call_user_func([$field, TreeParent::MACRO_METHOD])
        );
    }

    public function test_it_should_add_itself_as_a_many_to_one_macro()
    {
        TreeParent::enable();

        $relation = new ManyToOne(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), new DefaultNamingStrategy(), $this->fieldName, 'Foo'
        );

        $this->assertInstanceOf(
            TreeParent::class,
            call_user_func([$relation, TreeParent::MACRO_METHOD])
        );
    }

    public function test_can_mark_a_field_as_parent()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'parent' => 'parent',
        ]);
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
     * @return TreeParent
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
}
