<?php
namespace LaravelDoctrine\Fluent\Extensions\Gedmo\Blameable;

use Gedmo\Blameable\Mapping\Driver\Fluent;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Relations\ManyToOne;

class Extension implements Buildable
{
    const MACRO_METHOD = 'blameable';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $on;

    /**
     * @var string|array
     */
    private $trackedFields;

    /**
     * @var string
     */
    private $value;

    /**
     * Enable the extension.
     * 
     * @return void
     */
    public static function enable()
    {
        Field::macro(self::MACRO_METHOD, function(Field $builder){
            $extension = new static($builder->getClassMetadata(), $builder->getName());
            
            $builder->queue($extension);
            
            return $extension;
        });
        
        ManyToOne::macro(self::MACRO_METHOD, function(ManyToOne $builder){
            $joinColumn = $builder->getJoinColumn();
            $extension = new static($builder->getClassMetadata(), $joinColumn->getJoinColumn());
            
            $builder->queue($extension);
            
            return $extension;
        });
    }

    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName     = $fieldName;
    }

    /**
     * @return Extension
     */
    public function onCreate()
    {
        return $this->on('create');
    }

    /**
     * @return Extension
     */
    public function onUpdate()
    {
        return $this->on('update');
    }

    /**
     * @param array|string|null $fields
     * @param string|null       $value
     *
     * @return Extension
     */
    public function onChange($fields = null, $value = null)
    {
        return $this->on('change', $fields, $value);
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        if ($this->on === null) {
            throw new InvalidMappingException(
                "Field - [{$this->fieldName}] trigger 'on' is not one of [update, create, change] in class - {$this->classMetadata->name}");
        }

        if (is_array($this->trackedFields) && $this->value !== null) {
            throw new InvalidMappingException("Blameable extension does not support multiple value changeset detection yet.");
        }

        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, [
            $this->on => [
                $this->makeConfiguration(),
            ],
        ]);
    }

    /**
     * @param string            $on
     * @param array|string|null $fields
     * @param string|null       $value
     *
     * @return Fluent
     */
    private function on($on, $fields = null, $value = null)
    {
        $this->on            = $on;
        $this->trackedFields = $fields;
        $this->value         = $value;

        return $this;
    }

    /**
     * Returns either the field name on "create" and "update" blames, or the array configuration on "change".
     *
     * @return array|string
     */
    private function makeConfiguration()
    {
        if ($this->on == 'create' || $this->on == 'update') {
            return $this->fieldName;
        }

        return [
            'field'        => $this->fieldName,
            'trackedField' => $this->trackedFields,
            'value'        => $this->value,
        ];
    }
}
