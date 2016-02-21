<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Mapping\Driver\Fluent as FluentDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;

class TreePath implements Buildable
{
    const MACRO_METHOD = 'treePath';

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
    protected $separator;

    /**
     * @var mixed
     */
    protected $appendId = null;

    /**
     * @var bool
     */
    protected $startsWithSeparator = false;

    /**
     * @var bool
     */
    protected $endsWithSeparator = true;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string                  $fieldName
     * @param string                  $separator
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $fieldName, $separator = '|')
    {
        $this->classMetadata = $classMetadata;
        $this->fieldName     = $fieldName;
        $this->separator     = $separator;
    }

    /**
     * Enable TreePath
     */
    public static function enable()
    {
        Field::macro(self::MACRO_METHOD, function (Field $field, $separator = '|') {
            return new static($field->getClassMetadata(), $field->getName(), $separator);
        });
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        if (strlen($this->separator) > 1) {
            throw new InvalidMappingException("Tree Path field - [{$this->fieldName}] Separator {$this->separator} is invalid. It must be only one character long.");
        }

        $this->classMetadata->appendExtension($this->getExtensionName(), [
            'path'                       => $this->fieldName,
            'path_separator'             => $this->separator,
            'path_append_id'             => $this->appendId,
            'path_starts_with_separator' => $this->startsWithSeparator,
            'path_ends_with_separator'   => $this->endsWithSeparator
        ]);
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
     * @param  string   $separator
     * @return TreePath
     */
    public function separator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @param  mixed    $appendId
     * @return TreePath
     */
    public function appendId($appendId)
    {
        $this->appendId = $appendId;

        return $this;
    }

    /**
     * @param  bool     $startsWithSeparator
     * @return TreePath
     */
    public function startsWithSeparator($startsWithSeparator = true)
    {
        $this->startsWithSeparator = $startsWithSeparator;

        return $this;
    }

    /**
     * @param  bool     $endsWithSeparator
     * @return TreePath
     */
    public function endsWithSeparator($endsWithSeparator = true)
    {
        $this->endsWithSeparator = $endsWithSeparator;

        return $this;
    }
}
