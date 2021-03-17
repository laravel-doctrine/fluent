<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Uploadable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\UploadableFile;
use PHPUnit\Framework\TestCase;

class UploadableFileTest extends TestCase
{
    /**
     * @var string
     */
    private $fieldName = 'bar';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    protected function setUp(): void
    {
        $this->classMetadata = new ExtensibleClassMetadata("Foo");
    }

    /**
     * @dataProvider getTypes
     * 
     * @param string $type
     * @return void
     */
    public function test_it_adds_itself_as_a_field_macro_for_type($type)
    {
    	UploadableFile::enable();

        $field = Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', $this->fieldName);
        
        call_user_func([$field, "asFile$type"])->build();
        
        $this->assertExtension([
            "file{$type}Field" => $this->fieldName
        ]);
    }

    public function getTypes()
    {
        return [
            ['Path'],
            ['Name'],
            ['Size'],
            ['MimeType'],
        ];
    }
    
    
    public function test_it_holds_the_path()
    {
        $this->getBuilder("Path")->build();
        
        $this->assertExtension([
            'filePathField' => $this->fieldName
        ]);
    }
    
    public function test_it_holds_the_name()
    {
        $this->getBuilder("Name")->build();
        
        $this->assertExtension([
            'fileNameField' => $this->fieldName
        ]);
    }
    
    public function test_it_holds_the_size()
    {
        $this->getBuilder("Size")->build();
        
        $this->assertExtension([
            'fileSizeField' => $this->fieldName
        ]);
    }
    
    public function test_it_holds_the_mime_type()
    {
        $this->getBuilder("MimeType")->build();
        
        $this->assertExtension([
            'fileMimeTypeField' => $this->fieldName
        ]);
    }
    
    public function test_it_validates_the_type()
    {
        $this->expectException(InvalidMappingException::class);
        
        $this->getBuilder("Foo")->build();
    }
    
    public function test_it_merges_with_previous_extension_config()
    {
    	$this->classMetadata->addExtension(Fluent::EXTENSION_NAME, ['foo' => 'bar']);
        $this->getBuilder('Name')->build();
        
        $this->assertExtension([
            'foo' => 'bar',
            'fileNameField' => $this->fieldName
        ]);
    }
    
    /**
     * @param string $type
     *
     * @return UploadableFile
     */
    private function getBuilder($type)
    {
        return new UploadableFile($this->classMetadata, $this->fieldName, $type);
    }

    private function assertExtension($config)
    {
        $this->assertEquals($config, $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
}
