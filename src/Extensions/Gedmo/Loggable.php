<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Loggable\Mapping\Driver\Fluent;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;

class Loggable implements Buildable, Extension
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
        $this->logEntry = $logEntry;
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

        Versioned::enable();
    }

    /**
     * Execute the build process.
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
