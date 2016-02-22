<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Loggable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToOne;

class Loggable implements Buildable
{
    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @var string|null
     */
    private $logEntry;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     * @param string|null             $logEntry
     */
    public function __construct(ExtensibleClassMetadata $classMetadata, $logEntry = null)
    {
        $this->classMetadata = $classMetadata;
        $this->logEntry      = $logEntry;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        Builder::macro('loggable', function (Builder $builder, $logEntry = null) {
            $loggable = new static($builder->getClassMetadata(), $logEntry);
            $loggable->build();
        });

        Field::macro('versioned', function (Field $builder) {
            return new Versioned($builder->getClassMetadata(), $builder->getName());
        });

        ManyToOne::macro('versioned', function (ManyToOne $builder) {
            return new Versioned($builder->getClassMetadata(), $builder->getRelation());
        });

        OneToOne::macro('versioned', function (OneToOne $builder) {
            return new Versioned($builder->getClassMetadata(), $builder->getRelation());
        });
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        $config = [
            'loggable' => true,
        ];

        if ($this->logEntry !== null) {
            $config['logEntryClass'] = $this->logEntry;
        }

        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, array_merge(
            $this->classMetadata->getExtension(Fluent::EXTENSION_NAME),
            $config
        ));
    }
}
