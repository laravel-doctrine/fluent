<?php

namespace Tests\Extensions;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Gedmo\Translatable\Entity\Translation;
use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\GedmoExtensions;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use PHPUnit_Framework_TestCase;

class GedmoExtensionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getClasses
     */
    public function test_it_adds_this_class_with_a_fluent_driver_with_the_gedmo_entities($className)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAll($chain);

        $classes = $chain->getAllClassNames();
        $this->assertContains($className, $classes);
    }

    /**
     * @dataProvider getAbstractClasses
     */
    public function test_it_adds_this_abstract_class_with_a_fluent_driver_with_the_gedmo_entities($className)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAbstract($chain);

        $classes = $chain->getAllClassNames();
        $this->assertContains($className, $classes);
    }

    /**
     * @param string $method
     *
     * @dataProvider getFieldMethods
     */
    public function test_register_all_enables_all_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAll($chain);

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', 'foo');
        $this->assertTrue($field->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getFieldMethods
     */
    public function test_register_abstract_also_enables_all_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAbstract($chain);

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', 'foo');
        $this->assertTrue($field->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getBuilderMethods
     */
    public function test_register_all_enables_all_builder_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAll($chain);

        $builder = new Builder(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')));
        $this->assertTrue($builder->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getBuilderMethods
     */
    public function test_register_abstract_also_enables_all_builder_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAbstract($chain);

        $builder = new Builder(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')));
        $this->assertTrue($builder->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getManyToOneMethods
     */
    public function test_register_all_enables_all_many_to_one_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAll($chain);

        $builder = new ManyToOne(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            'foo',
            'Foo'
        );
        $this->assertTrue($builder->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getManyToOneMethods
     */
    public function test_register_abstract_also_enables_all_many_to_one_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAbstract($chain);

        $builder = new ManyToOne(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            'foo',
            'Foo'
        );
        $this->assertTrue($builder->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getManyToManyMethods
     */
    public function test_register_all_enables_all_many_to_many_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAll($chain);

        $builder = new ManyToMany(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            'foo',
            'Foo'
        );
        $this->assertTrue($builder->hasMacro($method));
    }

    /**
     * @param string $method
     *
     * @dataProvider getManyToManyMethods
     */
    public function test_register_abstract_also_enables_all_many_to_many_macro_methods($method)
    {
        $chain = new MappingDriverChain();

        GedmoExtensions::registerAbstract($chain);

        $builder = new ManyToMany(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            'foo',
            'Foo'
        );
        $this->assertTrue($builder->hasMacro($method));
    }

    public function getClasses()
    {
        return array_merge(
            [
                [LogEntry::class],
                [Translation::class],
            ],
            $this->getAbstractClasses()
        );
    }

    public function getAbstractClasses()
    {
        return [
            [AbstractLogEntry::class],
            [AbstractPersonalTranslation::class],
            [AbstractTranslation::class],
            [AbstractClosure::class],
        ];
    }

    public function getFieldMethods()
    {
        return [
            ['blameable'],
            ['ipTraceable'],
            ['locale'],
            ['sluggable'],
            ['softDelete'],
            ['sortableGroup'],
            ['sortablePosition'],
            ['timestampable'],
            ['translatable'],
            ['treeLeft'],
            ['treeLevel'],
            ['treeLockTime'],
            ['treeParent'],
            ['treePath'],
            ['treePathHash'],
            ['treePathSource'],
            ['treeRight'],
            ['treeRoot'],
            ['asFileMimeType'],
            ['asFileName'],
            ['asFilePath'],
            ['asFileSize'],
            ['versioned'],
        ];
    }

    public function getBuilderMethods()
    {
        return [
            ['loggable'],
            ['softDelete'],
            ['timestamps'],
            ['translationClass'],
            ['tree'],
            ['uploadable'],
        ];
    }

    public function getManyToOneMethods()
    {
        return [
            ['blameable'],
            ['sortableGroup'],
            ['treeParent'],
            ['treeRoot'],
            ['versioned'],
        ];
    }

    public function getManyToManyMethods()
    {
        return [
            ['sortableGroup'],
        ];
    }
}
