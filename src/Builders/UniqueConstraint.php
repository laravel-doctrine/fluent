<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Buildable;

class UniqueConstraint implements Buildable
{
    /**
     * @const
     */
    const SEPARATOR = '_';

    /**
     * @const
     */
    const SUFFIX = 'unique';

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
     * @param array                $columns
     */
    public function __construct(ClassMetadataBuilder $builder, array $columns)
    {
        $this->builder = $builder;
        $this->columns = $columns;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $this->builder->addUniqueConstraint(
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
     * @return array
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

        return $table . self::SEPARATOR . implode(self::SEPARATOR, $this->getColumns()) . self::SEPARATOR . self::SUFFIX;
    }
}
