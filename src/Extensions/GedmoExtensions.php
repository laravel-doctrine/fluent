<?php

namespace LaravelDoctrine\Fluent\Extensions;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use LaravelDoctrine\Fluent\FluentDriver;

class GedmoExtensions
{
    /**
     * Abstract Gedmo classes.
     *
     * @var string[]
     */
    protected static $abstract = [
        Gedmo\Mappings\Loggable\AbstractLogEntryMapping::class,
        Gedmo\Mappings\Translatable\AbstractPersonalTranslationMapping::class,
        Gedmo\Mappings\Translatable\AbstractTranslationMapping::class,
        Gedmo\Mappings\Tree\AbstractClosureMapping::class,
    ];

    /**
     * Concrete Gedmo classes.
     *
     * @var string[]
     */
    protected static $concrete = [
        Gedmo\Mappings\Loggable\LogEntryMapping::class,
        Gedmo\Mappings\Translatable\TranslationMapping::class,
    ];

    /**
     * @var Extension[]
     */
    protected static $extensions = [
        Gedmo\Blameable::class,
        Gedmo\IpTraceable::class,
        Gedmo\Loggable::class,
        Gedmo\Sluggable::class,
        Gedmo\SoftDeleteable::class,
        Gedmo\Sortable::class,
        Gedmo\Timestampable::class,
        Gedmo\Translatable::class,
        Gedmo\Tree::class,
        Gedmo\Uploadable::class,
    ];

    /**
     * Register all Gedmo classes on Fluent.
     *
     * @param MappingDriverChain $driverChain
     *
     * @return void
     *
     * @see \Gedmo\DoctrineExtensions::registerMappingIntoDriverChainORM
     */
    public static function registerAll(MappingDriverChain $driverChain)
    {
        self::register($driverChain, array_merge(self::$abstract, self::$concrete));
    }

    /**
     * Register only abstract Gedmo classes on Fluent.
     *
     * @param MappingDriverChain $driverChain
     *
     * @return void
     *
     * @see \Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM
     */
    public static function registerAbstract(MappingDriverChain $driverChain)
    {
        self::register($driverChain, self::$abstract);
    }

    /**
     * Register a new FluentDriver for the Gedmo namespace on the given chain.
     * Adds all extensions as macros.
     *
     * @param MappingDriverChain $driverChain
     * @param string[]           $mappings
     *
     * @return void
     */
    protected static function register(MappingDriverChain $driverChain, array $mappings)
    {
        $driverChain->addDriver(
            new FluentDriver($mappings),
            'Gedmo'
        );

        foreach (self::$extensions as $extension) {
            $extension::enable();
        }
    }
}
