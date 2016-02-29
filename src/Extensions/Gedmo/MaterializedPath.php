<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Fluent;

class MaterializedPath implements Buildable
{
    /**
     * @var Fluent
     */
    private $builder;

    /**
     * MaterializedPath constructor.
     *
     * @param Fluent $builder
     */
    public function __construct(Fluent $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Execute the build process
     */
    public function build()
    {
        /** @var ExtensibleClassMetadata $classMetadata */
        $classMetadata = $this->builder->getBuilder()->getClassMetadata();

        $classMetadata->appendExtension($this->getExtensionName(), [
            'strategy' => 'materializedPath',
        ]);
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TreeDriver::EXTENSION_NAME;
    }
}
