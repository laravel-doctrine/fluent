<?php

namespace LaravelDoctrine\Fluent\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\NamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Field;

class AttributeOverride implements Buildable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @param ClassMetadataBuilder $builder
     * @param NamingStrategy       $namingStrategy
     * @param string               $name
     * @param callable             $callback
     */
    public function __construct(
        ClassMetadataBuilder $builder,
        NamingStrategy $namingStrategy,
        $name,
        callable $callback
    ) {
        $this->builder = $builder;
        $this->callback = $callback;
        $this->name = $name;
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $callback = $this->callback;

        // We will create a new class metadata builder instance,
        // so we can use it to easily generated a new mapping
        // array, without re-declaring the existing field
        $builder = $this->newClassMetadataBuilder();

        $source = $this->convertToMappingArray($this->builder);

        // Create a new field builder for the new class metadata builder,
        // based on the existing (to be overridden) field
        $fieldBuilder = $this->getFieldBuilder(
            $builder,
            $source
        );

        $field = $callback($fieldBuilder);

        // When the user forget to return, use the Field instance
        // which contains the same information
        $field = $field ?: $fieldBuilder;

        if (!$field instanceof Field) {
            throw new InvalidArgumentException('The callback should return an instance of '.Field::class);
        }

        $field->build();

        $target = $this->convertToMappingArray($builder);

        $this->builder->getClassMetadata()->setAttributeOverride(
            $this->name,
            $this->mergeRecursively($source, $target)
        );
    }

    /**
     * @param ClassMetadataBuilder $builder
     * @param array                $mapping
     *
     * @return Field
     */
    protected function getFieldBuilder(ClassMetadataBuilder $builder, array $mapping)
    {
        return Field::make(
            $builder,
            $mapping['type'],
            $this->name
        );
    }

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     *
     * @return array
     */
    protected function convertToMappingArray(ClassMetadataBuilder $builder)
    {
        $metadata = $builder->getClassMetadata();

        return $metadata->getFieldMapping($this->name);
    }

    /**
     * @return ClassMetadataBuilder
     */
    protected function newClassMetadataBuilder()
    {
        return new ClassMetadataBuilder(
            new ClassMetadataInfo($this->builder->getClassMetadata()->name)
        );
    }

    /**
     * Merges the field mappings recursively, by keeping originals
     * settings, but replacing and adding new once.
     *
     * @param array $source
     * @param array $target
     *
     * @return array
     */
    protected function mergeRecursively(array $source, array $target)
    {
        foreach ($source as $key => $value) {
            if (!isset($target[$key])) {
                $target[$key] = $value;
            } elseif (is_array($value)) {
                $target[$key] = $this->mergeRecursively($value, $target[$key]);
            }
        }

        return $target;
    }
}
