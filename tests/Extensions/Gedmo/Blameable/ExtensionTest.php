<?php
namespace Tests\Extensions\Gedmo\Blameable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Blameable\Mapping\Driver\Fluent as Blameable;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Blameable\Extension;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use PHPUnit_Framework_TestCase;

class ExtensionTest extends PHPUnit_Framework_TestCase
{
    private $fieldName = 'bar';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var Extension
     */
    private $extension;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new Extension($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_fail_without_binding_which_event_to_use()
    {
        $this->setExpectedException(InvalidMappingException::class);

        $this->extension->build();
    }

    public function test_it_should_allow_blaming_on_create()
    {
        $this->extension->onCreate()->build();

        $this->assertBuildResultIs([
            'create' => [
                $this->fieldName,
            ],
        ]);
    }

    public function test_it_should_allow_blaming_on_updates()
    {
        $this->extension->onUpdate()->build();

        $this->assertBuildResultIs([
            'update' => [
                $this->fieldName,
            ],
        ]);
    }

    public function test_it_should_allow_blaming_on_specific_changes()
    {
        $this->extension->onChange()->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => null,
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_a_specific_field_for_changes()
    {
        $this->extension->onChange('foobar')->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => 'foobar',
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_a_specific_field_with_a_specific_value_for_changes()
    {
        $this->extension->onChange('foobar', 'Published')->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => 'foobar',
                    'value'        => 'Published',
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_an_array_of_fields_for_changes()
    {
        $this->extension->onChange(['foobar', 'baz'])->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => ['foobar', 'baz'],
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_not_allow_tracking_an_array_of_fields_with_a_specific_value_for_changes()
    {
        $this->setExpectedException(InvalidMappingException::class);

        $this->extension->onChange(['foobar', 'baz'], 'this_will_explode')->build();
    }
    
    public function test_it_should_add_itself_as_a_field_macro()
    {
    	Extension::enable();
        
        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', 'foo');
        
        $this->assertInstanceOf(
            Extension::class, 
            call_user_func([$field, Extension::MACRO_METHOD])
        );
    }
    
    public function test_it_should_add_itself_as_a_many_to_one_macro()
    {
    	Extension::enable();
        
        $manyToOne = new ManyToOne(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            'foo',
            'Bar'
        );
        
        $this->assertInstanceOf(
            Extension::class, 
            call_user_func([$manyToOne, Extension::MACRO_METHOD])
        );
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
        $this->assertEquals($expected, $this->classMetadata->getExtension(Blameable::EXTENSION_NAME));
    }
}
