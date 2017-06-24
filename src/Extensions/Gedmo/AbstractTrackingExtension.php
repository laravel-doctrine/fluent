<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;

abstract class AbstractTrackingExtension
{
    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string
     */
    protected $on;

    /**
     * @var string|array
     */
    protected $trackedFields;

    /**
     * @var string
     */
    protected $value;

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    abstract protected function getExtensionName();

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName = $fieldName;
    }

    /**
     * @return $this
     */
    public function onCreate()
    {
        return $this->on('create');
    }

    /**
     * @return $this
     */
    public function onUpdate()
    {
        return $this->on('update');
    }

    /**
     * @param array|string|null $fields
     * @param string|null       $value
     *
     * @return $this
     */
    public function onChange($fields = null, $value = null)
    {
        return $this->on('change', $fields, $value);
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        if ($this->on === null) {
            throw new InvalidMappingException(
                "Field - [{$this->fieldName}] trigger 'on' is not one of [update, create, change] in class - {$this->classMetadata->name}");
        }

        if (is_array($this->trackedFields) && $this->value !== null) {
            throw new InvalidMappingException('Extension does not support multiple value change-set detection yet.');
        }

        $this->classMetadata->appendExtension($this->getExtensionName(), [
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
     * @return $this
     */
    protected function on($on, $fields = null, $value = null)
    {
        $this->on = $on;
        $this->trackedFields = $fields;
        $this->value = $value;

        return $this;
    }

    /**
     * Returns either the field name on "create" and "update", or the array configuration on "change".
     *
     * @return array|string
     */
    protected function makeConfiguration()
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
