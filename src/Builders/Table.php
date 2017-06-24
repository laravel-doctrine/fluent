<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Traits\Queueable;

class Table extends AbstractBuilder implements Buildable
{
    use Queueable {
        build as buildQueued;
    }

    /**
     * @var array
     */
    protected $primaryTable = [];

    /**
     * @param ClassMetadataBuilder $builder
     * @param string|callable|null $name
     */
    public function __construct(ClassMetadataBuilder $builder, $name = null)
    {
        parent::__construct($builder);

        if (is_callable($name)) {
            $name($this);
        } else {
            $this->setName($name);
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->builder->setTable($name);

        return $this;
    }

    /**
     * @param string $schema
     *
     * @return $this
     */
    public function schema($schema)
    {
        $this->primaryTable['schema'] = $schema;

        return $this;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function charset($charset)
    {
        $this->option('charset', $charset);

        return $this;
    }

    /**
     * @param string $collate
     *
     * @return $this
     */
    public function collate($collate)
    {
        $this->option('collate', $collate);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options = [])
    {
        $this->primaryTable['options'] = $options;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function option($name, $value)
    {
        $this->primaryTable['options'][$name] = $value;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->getClassMetadata()->setPrimaryTable($this->primaryTable);
    }
}
