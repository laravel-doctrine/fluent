<?php
namespace Tests\Extensions\Gedmo\IpTraceable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\IpTraceable\Mapping\Driver\Fluent as IpTraceable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;
use LaravelDoctrine\Fluent\Extensions\Gedmo\IpTraceable\Extension;
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
        return IpTraceable::EXTENSION_NAME;
    }
}
