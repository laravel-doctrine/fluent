<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Loggable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Loggable;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use PHPUnit_Framework_TestCase;

class LoggableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Loggable
     */
    private $loggable;

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    protected function setUp()
    {
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->loggable      = new Loggable($this->classMetadata);
    }

    public function test_it_should_mark_the_entity_as_loggable()
    {
        $this->loggable->build();

        $this->assertEquals([
            'loggable' => true,
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_allows_customizing_the_log_entry_class()
    {
        $loggable = new Loggable($this->classMetadata, 'CustomLogEntry');

        $loggable->build();

        $this->assertEquals([
            'loggable'      => true,
            'logEntryClass' => 'CustomLogEntry',
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_respects_previous_configurations()
    {
        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, [
            'versioned' => ['foo'],
        ]);

        $this->loggable->build();

        $this->assertEquals([
            'loggable'  => true,
            'versioned' => ['foo'],
        ], $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    public function test_it_should_add_itself_as_a_builder_macro()
    {
    	Loggable::enable();
        
        $entity = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy());
        
        $entity->loggable();
        
        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
    
    public function test_it_should_add_versioned_as_a_field_macro()
    {
    	Loggable::enable();
        
        $field = Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'foo');
        
        $field->versioned();
        $field->build();
        
        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
    
    public function test_it_should_add_versioned_as_a_many_to_one_macro()
    {
    	Loggable::enable();
        
        $relation = new ManyToOne(
            new ClassMetadataBuilder($this->classMetadata),
            new DefaultNamingStrategy(),
            'someRelation',
            'SomeEntity'
        );
        
        
        $relation->versioned();
        $relation->build();
        
        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
    
    public function test_it_should_add_versioned_as_a_one_to_one_macro()
    {
    	Loggable::enable();
        
        $relation = new OneToOne(
            new ClassMetadataBuilder($this->classMetadata),
            new DefaultNamingStrategy(),
            'someRelation',
            'SomeEntity'
        );
        
        
        $relation->versioned();
        $relation->build();
        
        $this->assertNotNull($this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }
    
}
