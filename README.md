# Fluent Mapping Driver

[![GitHub release](https://img.shields.io/github/release/laravel-doctrine/doctrine-fluent-mapping-driver.svg?style=flat-square)](https://packagist.org/packages/laravel-doctrine/doctrine-fluent-mapping-driver)
[![Travis](https://img.shields.io/travis/laravel-doctrine/doctrine-fluent-mapping-driver.svg?style=flat-square)](https://travis-ci.org/laravel-doctrine/doctrine-fluent-mapping-driver)
[![StyleCI](https://styleci.io/repos/42745661/shield)](https://styleci.io/repos/42745661)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/laravel-doctrine/doctrine-fluent-mapping-driver.svg?style=flat-square)](https://github.com/laravel-doctrine/doctrine-fluent-mapping-driver)
[![Packagist](https://img.shields.io/packagist/dm/laravel-doctrine/doctrine-fluent-mapping-driver.svg?style=flat-square)](https://packagist.org/packages/laravel-doctrine/doctrine-fluent-mapping-driver)
[![Packagist](https://img.shields.io/packagist/dt/laravel-doctrine/doctrine-fluent-mapping-driver.svg?style=flat-square)](https://packagist.org/packages/laravel-doctrine/doctrine-fluent-mapping-driver)

This mapping driver allows you to manage your mappings in an Object Oriented approach, by implementing FluentMapping and EmbeddableMapping interfaces.

The package also provides a fluent Builder over Doctrine's ClassMetadataBuilder, aimed at easing usage of Doctrine's mapping concepts in Laravel.

## Standalone usage

You need to set the `LaravelDoctrine\Fluent\FluentMappingDriver` as your MappingDriver implementation in your EntityManager

```
$configuration = new Configuration();
$driver = new FluentMappingDriver($configuration->getNamingStrategy());
$configuration->setMetadataDriverImpl($driver);
```

Then add as many mapping files as you need:

```
$driver->addMapping(new UserMapping);
$driver->addMapping(new RoleMapping);
$driver->addMapping(new FooMapping);
$driver->addMapping(new FooMapping);
```
