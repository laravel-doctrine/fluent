<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Buildable;

class GeneratedValue implements Buildable
{
    /**
     * @var FieldBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $strategy = 'AUTO';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $initial = 1;

    /**
     * @var int
     */
    protected $size = 10;

    /**
     * @var string
     */
    protected $generator;

    /**
     * @var ClassMetadataInfo
     */
    protected $classMetadata;

    /**
     * @param FieldBuilder      $builder
     * @param ClassMetadataInfo $classMetadata
     */
    public function __construct(FieldBuilder $builder, ClassMetadataInfo $classMetadata)
    {
        $this->builder = $builder;
        $this->classMetadata = $classMetadata;
    }

    /**
     * Tells Doctrine to pick the strategy that is preferred by the used database platform. The preferred strategies
     * are IDENTITY for MySQL, SQLite, MsSQL and SQL Anywhere and SEQUENCE for Oracle and PostgreSQL. This strategy
     * provides full portability.
     *
     * @param string|null $name
     * @param string|null $initial
     * @param string|null $size
     *
     * @return $this
     */
    public function auto($name = null, $initial = null, $size = null)
    {
        $this->strategy = 'AUTO';
        $this->customize($name, $initial, $size);

        return $this;
    }

    /**
     * Tells Doctrine to use a database sequence for ID generation. This strategy does currently not provide full
     * portability. Sequences are supported by Oracle, PostgreSql and SQL Anywhere.
     *
     * @param string|null $name
     * @param string|null $initial
     * @param string|null $size
     *
     * @return $this
     */
    public function sequence($name = null, $initial = null, $size = null)
    {
        $this->strategy = 'SEQUENCE';
        $this->customize($name, $initial, $size);

        return $this;
    }

    /**
     * Tells Doctrine to use special identity columns in the database that generate a value on insertion of a row.
     * This strategy does currently not provide full portability and is supported by the following platforms:
     * MySQL/SQLite/SQL Anywhere (AUTO_INCREMENT), MSSQL (IDENTITY) and PostgreSQL (SERIAL).
     *
     * @return $this
     */
    public function identity()
    {
        $this->strategy = 'IDENTITY';

        return $this;
    }

    /**
     * Tells Doctrine to use the built-in Universally Unique Identifier generator.
     * This strategy provides full portability.
     *
     * @return $this
     */
    public function uuid()
    {
        $this->strategy = 'UUID';

        return $this;
    }

    /**
     * Tells Doctrine that the identifiers are assigned (and thus generated) by your code. The assignment must take
     * place before a new entity is passed to EntityManager#persist.
     * NONE is the same as leaving off the @GeneratedValue entirely.
     *
     * @return $this
     */
    public function none()
    {
        $this->strategy = 'NONE';

        return $this;
    }

    /**
     * Tells Doctrine to use a custom Generator class to generate identifiers.
     * The given class must extend \Doctrine\ORM\Id\AbstractIdGenerator.
     *
     * @param string $generatorClass
     *
     * @return $this
     */
    public function custom($generatorClass)
    {
        $this->strategy = 'CUSTOM';
        $this->generator = $generatorClass;

        return $this;
    }

    /**
     * @param string|null $name
     * @param string|null $initial
     * @param string|null $size
     */
    private function customize($name, $initial, $size)
    {
        $this->name = $name ?: $this->name;
        $this->initial = $initial ?: $this->initial;
        $this->size = $size ?: $this->size;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->generatedValue($this->strategy);

        if ($this->name) {
            $this->builder->setSequenceGenerator($this->name, $this->size, $this->initial);
        }

        if ($this->generator) {
            $this->classMetadata->setCustomGeneratorDefinition(['class' => $this->generator]);
        }
    }
}
