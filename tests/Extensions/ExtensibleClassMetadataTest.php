<?php
namespace Tests\Extensions;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as ClassMetadataImplementation;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use PHPUnit_Framework_TestCase;

class ExtensibleClassMetadataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ExtensibleClassMetadata
     */
    private $cm;

    protected function setUp()
    {
        $this->cm = new ExtensibleClassMetadata("Foo");
    }
    
    public function test_it_should_be_a_doctrine_class_metadata()
    {
        $this->assertInstanceOf(ClassMetadata::class, $this->cm);
        $this->assertInstanceOf(ClassMetadataImplementation::class, $this->cm);
    }
    
    public function test_it_should_hold_extension_information()
    {
    	$this->cm->addExtension('foo', [
            'bar' => 'baz'
        ]);
        
        $this->assertNotEmpty($this->cm->extensions);
        $this->assertEquals(['bar' => 'baz'], $this->cm->getExtension('foo'));
    }
}