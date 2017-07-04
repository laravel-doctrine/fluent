<?php

namespace LaravelDoctrine\Fluent\Builders;

use BadMethodCallException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;
use LaravelDoctrine\Fluent\Builders\Traits\Queueable;
use LaravelDoctrine\Fluent\Builders\Traits\QueuesMacros;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\GedmoFieldHints;

/**
 * @method $this unique(boolean $flag = true)   Boolean value to determine if the value of the column should be unique
 *                                              across all rows of the underlying entities table.
 * @method $this nullable(boolean $flag = true) Determines if NULL values are allowed for this column.
 * @method $this length(int $length)            Used by the “string” type to determine its maximum length in the
 *                                              database. Doctrine does not validate the length of string values
 *                                              for you.
 * @method $this columnName(string $column)     By default the property name is used for the database column name also,
 *                                              however the ‘name’ attribute allows you to determine the column name.
 * @method $this precision(int $precision)      The precision for a decimal (exact numeric) column (applies only for
 *                                              decimal column), which is the maximum number of digits that are stored
 *                                              for the values.
 * @method $this scale(int $scale)              The scale for a decimal (exact numeric) column (applies only for
 *                                              decimal column), which represents the number of digits to the right of
 *                                              the decimal point and must not be greater than precision.
 * @method $this default(string $default)       The default value to set for the column if no value is supplied.
 * @method $this columnDefinition(string $def)  DDL SQL snippet that starts after the column name and specifies the
 *                                              complete (non-portable!) column definition. This attribute allows to
 *                                              make use of advanced RMDBS features. However you should make careful
 *                                              use of this feature and the consequences. SchemaTool will not detect
 *                                              changes on the column correctly anymore if you use “columnDefinition”.
 * @method $this option($name, $value)          Set custom options
 */
class Field implements Buildable
{
    use Macroable;
    use Queueable {
        build as buildQueued;
    }
    use QueuesMacros;
    use GedmoFieldHints;

    /**
     * @var FieldBuilder
     */
    protected $fieldBuilder;

    /**
     * @var ClassMetadataBuilder
     */
    protected $metaDatabuilder;

    /**
     * @var ClassMetadataInfo
     */
    protected $classMetadata;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * Protected constructor to force usage of factory method.
     *
     * @param FieldBuilder         $fieldBuilder
     * @param ClassMetadataBuilder $builder
     * @param Type                 $type
     * @param string               $name
     */
    protected function __construct(FieldBuilder $fieldBuilder, ClassMetadataBuilder $builder, Type $type, $name)
    {
        $this->fieldBuilder = $fieldBuilder;
        $this->metaDatabuilder = $builder;
        $this->classMetadata = $builder->getClassMetadata();
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * @param ClassMetadataBuilder $builder
     * @param string               $type
     * @param string               $name
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return Field
     */
    public static function make(ClassMetadataBuilder $builder, $type, $name)
    {
        $type = Type::getType($type);

        $field = $builder->createField($name, $type->getName());

        return new static($field, $builder, $type, $name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ClassMetadata|ExtensibleClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /**
     * By default the property name is used for the database column name also, however the ‘name’ attribute
     * allows you to determine the column name.
     *
     * @param string $columnName
     *
     * @return $this
     */
    public function name($columnName)
    {
        $this->columnName($columnName);

        return $this;
    }

    /**
     * @return Field
     */
    public function autoIncrement()
    {
        $this->generatedValue();

        return $this;
    }

    /**
     * @param callable|null $callback
     *
     * @return Field
     */
    public function generatedValue(callable $callback = null)
    {
        $generatedValue = new GeneratedValue($this->fieldBuilder, $this->classMetadata);

        if ($callback) {
            $callback($generatedValue);
        }

        $generatedValue->build();

        return $this;
    }

    /**
     * Boolean value to determine if the column should be capable of representing only non-negative integers
     * (applies only for integer column and might not be supported by all vendors).
     *
     * @return Field
     */
    public function unsigned()
    {
        $this->fieldBuilder->option('unsigned', true);

        return $this;
    }

    /**
     * Boolean value to determine if the specified length of a string column should be fixed or varying
     * (applies only for string/binary column and might not be supported by all vendors).
     *
     * @param bool $fixed
     *
     * @return Field
     */
    public function fixed($fixed)
    {
        $this->fieldBuilder->option('fixed', $fixed);

        return $this;
    }

    /**
     * The comment of the column in the schema (might not be supported by all vendors).
     *
     * @param string $comment
     *
     * @return Field
     */
    public function comment($comment)
    {
        $this->fieldBuilder->option('comment', $comment);

        return $this;
    }

    /**
     * The collation of the column (only supported by Drizzle, Mysql, PostgreSQL>=9.1, Sqlite and SQLServer).
     *
     * @param string $collation
     *
     * @return Field
     */
    public function collation($collation)
    {
        $this->fieldBuilder->option('collation', $collation);

        return $this;
    }

    /**
     * @return Field
     */
    public function primary()
    {
        $this->fieldBuilder->makePrimaryKey();

        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return Field
     */
    public function index($name = null)
    {
        $index = new Index(
            $this->metaDatabuilder,
            [$this->getName()]
        );

        if ($name !== null) {
            $index->name($name);
        }

        $this->callbackAndQueue($index);

        return $this;
    }

    /**
     * @return Field
     */
    public function useForVersioning()
    {
        $this->fieldBuilder->isVersionField();

        return $this;
    }

    /**
     * @return Field
     */
    public function build()
    {
        $this->fieldBuilder->build();

        $this->buildQueued();

        return $this;
    }

    /**
     * @return FieldBuilder
     */
    public function getBuilder()
    {
        return $this->fieldBuilder;
    }

    /**
     * Magic call method works as a proxy for the Doctrine FieldBuilder.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws BadMethodCallException
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        // Work around reserved keywords
        if ($method === 'default') {
            return call_user_func_array([$this, 'setDefault'], $args);
        }

        if ($this->hasMacro($method)) {
            return $this->queueMacro($method, $args);
        }

        if (method_exists($this->getBuilder(), $method)) {
            call_user_func_array([$this->getBuilder(), $method], $args);

            return $this;
        }

        throw new BadMethodCallException("FieldBuilder method [{$method}] does not exist.");
    }

    /**
     * The default value to set for the column if no value is supplied.
     *
     * @param string $default
     *
     * @return Field
     */
    protected function setDefault($default)
    {
        $this->fieldBuilder->option('default', $default);

        return $this;
    }
}
