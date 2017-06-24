<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Buildable;

class Index implements Buildable
{
    /**
     * @var string
     */
    protected $separator = '_';

    /**
     * Suffix to be added to the index key name.
     *
     * @var string
     */
    protected $suffix = 'index';

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string|null
     */
    protected $name = null;

    /**
     * @param ClassMetadataBuilder $builder
     * @param string[]             $columns
     */
    public function __construct(ClassMetadataBuilder $builder, array $columns)
    {
        $this->builder = $builder;
        $this->columns = $columns;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->addIndex(
            $this->getColumns(),
            $this->getName()
        );
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name ?: $this->generateIndexName();
    }

    /**
     * @return string
     */
    protected function generateIndexName()
    {
        $table = $this->builder->getClassMetadata()->getTableName();

        return $table.$this->separator.implode($this->separator, $this->getColumns()).$this->separator.$this->suffix;
    }
}
