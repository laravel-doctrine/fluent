<?php

namespace Gedmo;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Gedmo\Mapping\Driver;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\FluentDriver;

abstract class FluentExtension implements Driver
{
    /**
     * @var FluentDriver
     */
    protected $originalDriver;

    /**
     * @return string
     */
    abstract protected function getExtensionName();

    /**
     * Read extended metadata configuration for
     * a single mapped class.
     *
     * @param ExtensibleClassMetadata $meta
     * @param array                   $config
     *
     * @return void
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        if (!$meta instanceof ExtensibleClassMetadata) {
            return;
        }

        $config = array_merge_recursive($config, $meta->getExtension(
            $this->getExtensionName()
        ));
    }

    /**
     * Make sure the original driver is Fluent.
     *
     * @param MappingDriver $driver
     *
     * @return void
     */
    public function setOriginalDriver($driver)
    {
        $this->originalDriver = $this->extractFluentDriver($driver);
    }

    /**
     * @param MappingDriver $driver
     *
     * @return FluentDriver
     */
    private function extractFluentDriver(MappingDriver $driver)
    {
        if ($driver instanceof FluentDriver) {
            return $driver;
        }

        if ($driver instanceof MappingDriverChain) {
            $default = $driver->getDefaultDriver();
            if ($default instanceof FluentDriver) {
                return $default;
            }

            foreach ($driver->getDrivers() as $namespace => $driver) {
                if ($driver instanceof FluentDriver) {
                    return $driver;
                }
            }
        }

        throw new \UnexpectedValueException('Fluent driver not found in the driver chain.');
    }
}
