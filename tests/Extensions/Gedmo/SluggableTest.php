<?php
namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Sluggable\Mapping\Driver\Fluent as SluggableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Sluggable;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class SluggableTest extends \PHPUnit_Framework_TestCase
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
     * @var Sluggable
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'slug';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'slug')->build();

        $this->extension     = new Sluggable($this->classMetadata, $this->fieldName, 'name');
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        Sluggable::enable();

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', $this->fieldName)->build();

        $this->assertInstanceOf(
            Sluggable::class,
            call_user_func([$field, Sluggable::MACRO_METHOD], 'name')
        );
    }

    public function test_can_only_make_a_valid_field_sluggable()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Sluggable field is not a valid field type');

        Sluggable::enable();

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'smallint', $this->fieldName)->build();
        call_user_func([$field, Sluggable::MACRO_METHOD], 'name');
    }


    public function test_it_should_add_sluggable_to_the_given_field()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'fields'      => ['name'],
            'handlers'    => [],
            'slug'        => $this->fieldName,
            'style'       => 'default',
            'dateFormat'  => 'Y-m-d-H:i',
            'updatable'   => true,
            'unique'      => true,
            'unique_base' => null,
            'separator'   => '-',
            'prefix'      => '',
            'suffix'      => ''
        ]);
    }

    public function test_can_set_custom_settingss()
    {
        $this->getExtension()
             ->baseOn('custom')
             ->handlers('Handler')
             ->style('other')
             ->dateFormat('Y-m')
             ->updatable(false)
             ->unique(false)
             ->uniqueBase('base')
             ->separator('_')
             ->prefix('prefix-')
             ->suffix('-suffix')
             ->build();

        $this->assertBuildResultIs([
            'fields'      => ['custom'],
            'handlers'    => ['Handler'],
            'slug'        => $this->fieldName,
            'style'       => 'other',
            'dateFormat'  => 'Y-m',
            'updatable'   => false,
            'unique'      => false,
            'unique_base' => 'base',
            'separator'   => '_',
            'prefix'      => 'prefix-',
            'suffix'      => '-suffix'
        ]);
    }

    public function can_have_many_sluggable_fields()
    {
        $slug1 = new Sluggable($this->classMetadata, 'slug1', 'name');
        $slug2 = new Sluggable($this->classMetadata, 'slug2', 'name');

        $slug1->build();
        $this->assertCount(1, $this->classMetadata->getExtension($this->getExtensionName())['slugs']);

        $slug1->build();

        $this->assertCount(2, $this->classMetadata->getExtension($this->getExtensionName())['slugs']);
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
        $this->assertEquals([
            'slugs' => [
                $this->fieldName => $expected
            ]
        ], $this->classMetadata->getExtension(
            $this->getExtensionName()
        ));
    }

    /**
     * @return Sluggable
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
        return SluggableDriver::EXTENSION_NAME;
    }
}
