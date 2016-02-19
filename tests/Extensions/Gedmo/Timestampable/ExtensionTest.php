<?php
namespace Tests\Extensions\Gedmo\Timestampable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Timestampable\Mapping\Driver\Fluent as Timestampable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Timestampable\Extension;
use PHPUnit_Framework_TestCase;
use Tests\Extensions\Gedmo\TrackingExtensions;

class ExtensionTest extends PHPUnit_Framework_TestCase
{
    use TrackingExtensions;
    
    /**
     * @var Extension
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'ip';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new Extension($this->classMetadata, $this->fieldName);
    }
    
    public function test_it_should_add_itself_as_a_field_macro()
    {
        Extension::enable();

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', $this->fieldName);

        $this->assertInstanceOf(
            Extension::class,
            call_user_func([$field, Extension::MACRO_METHOD])
        );

        $builder = new Builder($cmb = new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')));

        $this->assertNull(
            call_user_func([$builder, 'timestamps'])
        );
    }
    

    /**
     * @return AbstractTrackingExtension
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
        return Timestampable::EXTENSION_NAME;
    }
}
