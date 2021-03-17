<?php
namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Blameable\Mapping\Driver\Fluent as BlameableDriver;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Blameable;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use PHPUnit\Framework\TestCase;

class BlameableTest extends TestCase
{
    use TrackingExtensions;
    
    /**
     * @var Blameable
     */
    private $extension;

    protected function setUp(): void
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new Blameable($this->classMetadata, $this->fieldName);
    }
    
    public function test_it_should_add_itself_as_a_field_macro()
    {
    	Blameable::enable();
        
        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', $this->fieldName);
        
        $this->assertInstanceOf(
            Blameable::class, 
            call_user_func([$field, Blameable::MACRO_METHOD])
        );
    }
    
    public function test_it_should_add_itself_as_a_many_to_one_macro()
    {
    	Blameable::enable();
        
        $manyToOne = new ManyToOne(
            new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')),
            new DefaultNamingStrategy(),
            $this->fieldName,
            'Bar'
        );
        
        $this->assertInstanceOf(
            Blameable::class, 
            call_user_func([$manyToOne, Blameable::MACRO_METHOD])
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
        return BlameableDriver::EXTENSION_NAME;
    }
}
