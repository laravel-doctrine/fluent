<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Gedmo\Exception\InvalidArgumentException;
use Gedmo\Sluggable\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;

class Sluggable implements Buildable, Extension
{
    const MACRO_METHOD = 'sluggable';

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @var string
     */
    protected $style = 'default';

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d-H:i';

    /**
     * @var bool
     */
    protected $updatable = true;

    /**
     * @var bool
     */
    protected $unique = true;

    /**
     * @var null
     */
    protected $unique_base = null;

    /**
     * @var string
     */
    protected $separator = '-';

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $suffix = '';

    /**
     * List of types which are valid for slug and sluggable fields.
     *
     * @var array
     */
    private $validTypes = [
        'string',
        'text',
        'integer',
        'int',
        'datetime',
        'citext',
    ];

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     * @param array|string            $fields
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName, $fields)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName = $fieldName;
        $this->baseOn($fields);
    }

    /**
     * Return the name of the actual extension.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return FluentDriver::EXTENSION_NAME;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        Field::macro(static::MACRO_METHOD, function (Field $builder, $fields) {
            return new static($builder->getClassMetadata(), $builder->getName(), $fields);
        });
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->isValidField($this->classMetadata, $this->fieldName);

        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'slugs' => [
                $this->fieldName => $this->makeConfiguration(),
            ],
        ]);
    }

    /**
     * @param array|string $fields
     *
     * @return Sluggable
     */
    public function baseOn($fields)
    {
        $this->fields = is_array($fields) ? $fields : [$fields];

        return $this;
    }

    /**
     * @param array|string $handlers
     *
     * @return Sluggable
     */
    public function handlers($handlers)
    {
        $this->handlers = is_array($handlers) ? $handlers : [$handlers];

        return $this;
    }

    /**
     * @param string $style
     *
     * @return Sluggable
     */
    public function style($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @param string $dateFormat
     *
     * @return Sluggable
     */
    public function dateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @param bool $updatable
     *
     * @return Sluggable
     */
    public function updatable($updatable = true)
    {
        $this->updatable = $updatable;

        return $this;
    }

    /**
     * @param bool $unique
     *
     * @return Sluggable
     */
    public function unique($unique = true)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @param null $unique_base
     *
     * @return Sluggable
     */
    public function uniqueBase($unique_base)
    {
        $this->unique_base = $unique_base;

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return Sluggable
     */
    public function separator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return Sluggable
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return Sluggable
     */
    public function suffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Checks if $field type is valid as Sluggable field.
     *
     * @param ClassMetadataInfo $meta
     * @param string            $field
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    protected function isValidField(ClassMetadataInfo $meta, $field)
    {
        $mapping = $meta->getFieldMapping($field);

        if (!$mapping || !in_array($mapping['type'], $this->validTypes)) {
            throw new InvalidArgumentException('Sluggable field is not a valid field type');
        }

        return true;
    }

    /**
     * @return array
     */
    private function makeConfiguration()
    {
        return [
            'fields'      => $this->fields,
            'handlers'    => $this->handlers,
            'slug'        => $this->fieldName,
            'style'       => $this->style,
            'dateFormat'  => $this->dateFormat,
            'updatable'   => $this->updatable,
            'unique'      => $this->unique,
            'unique_base' => $this->unique_base,
            'separator'   => $this->separator,
            'prefix'      => $this->prefix,
            'suffix'      => $this->suffix,
        ];
    }
}
