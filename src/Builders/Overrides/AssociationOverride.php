<?php

namespace LaravelDoctrine\Fluent\Builders\Overrides;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\NamingStrategy;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\Relation;

class AssociationOverride implements Buildable
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
     * @var array
     */
    protected $relations = [
        ClassMetadataInfo::MANY_TO_ONE  => ManyToOne::class,
        ClassMetadataInfo::MANY_TO_MANY => ManyToMany::class,
    ];

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
        // array, without re-declaring the existing association
        $builder = $this->newClassMetadataBuilder();
        $source = $this->convertToMappingArray($this->builder);

        if (!isset($this->relations[$source['type']])) {
            throw new InvalidArgumentException('Only ManyToMany and ManyToOne relations can be overridden');
        }

        // Create a new association builder, based on the given type
        $associationBuilder = $this->getAssociationBuilder($builder, $source);

        // Give the original join table name, so we won't
        // accidentally remove custom join table names
        if ($this->hasJoinTable($source)) {
            $associationBuilder->setJoinTable($source['joinTable']['name']);
        }

        $association = $callback($associationBuilder);

        // When the user forget to return, use the $associationBuilder instance
        // which contains the same information
        $association = $association ?: $associationBuilder;

        if (!$association instanceof Relation) {
            throw new InvalidArgumentException('The callback should return an instance of '.Relation::class);
        }

        $association->build();

        $target = $this->convertToMappingArray($builder);

        $overrideMapping = [];

        // ManyToMany mappings
        if ($this->hasJoinTable($target)) {
            $overrideMapping['joinTable'] = $this->mapJoinTable(
                $target['joinTable'],
                $source['joinTable']
            );
        }

        // ManyToOne mappings
        if ($this->hasJoinColumns($target)) {
            $overrideMapping['joinColumns'] = $this->mapJoinColumns(
                $target['joinColumns'],
                $source['joinColumns']
            );
        }

        $this->builder->getClassMetadata()->setAssociationOverride(
            $this->name,
            $overrideMapping
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

        return $metadata->getAssociationMapping($this->name);
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
     * @param $builder
     * @param $source
     *
     * @return mixed
     */
    protected function getAssociationBuilder(ClassMetadataBuilder $builder, array $source)
    {
        return new $this->relations[$source['type']](
            $builder,
            $this->namingStrategy,
            $this->name,
            $source['targetEntity']
        );
    }

    /**
     * @param array $target
     * @param array $source
     *
     * @return array
     */
    protected function mapJoinTable(array $target = [], array $source = [])
    {
        $joinTable['name'] = $target['name'];

        if ($this->hasJoinColumns($target)) {
            $joinTable['joinColumns'] = $this->mapJoinColumns(
                $target['joinColumns'],
                $source['joinColumns']
            );
        }

        if ($this->hasInverseJoinColumns($target)) {
            $joinTable['inverseJoinColumns'] = $this->mapJoinColumns(
                $target['inverseJoinColumns'],
                $source['inverseJoinColumns']
            );
        }

        return $joinTable;
    }

    /**
     * @param array $target
     * @param array $source
     *
     * @return mixed
     *
     * @internal param $target
     * @internal param $source
     * @internal param $overrideMapping
     */
    protected function mapJoinColumns(array $target = [], array $source = [])
    {
        $joinColumns = [];
        foreach ($target as $index => $joinColumn) {
            if (isset($source[$index])) {
                $diff = array_diff($joinColumn, $source[$index]);

                if (!empty($diff)) {
                    $joinColumns[] = $diff;
                }
            }
        }

        return $joinColumns;
    }

    /**
     * @param array $target
     *
     * @return bool
     */
    protected function hasJoinColumns(array $target = [])
    {
        return isset($target['joinColumns']);
    }

    /**
     * @param array $target
     *
     * @return bool
     */
    protected function hasInverseJoinColumns(array $target = [])
    {
        return isset($target['inverseJoinColumns']);
    }

    /**
     * @param array $target
     *
     * @return bool
     */
    protected function hasJoinTable(array $target = [])
    {
        return isset($target['joinTable']);
    }
}
