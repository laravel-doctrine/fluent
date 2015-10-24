# Fluent Mapping Driver

[![GitHub release](https://img.shields.io/github/release/laravel-doctrine/fluent.svg?style=flat-square)](https://packagist.org/packages/laravel-doctrine/fluent)
[![Travis](https://img.shields.io/travis/laravel-doctrine/fluent.svg?style=flat-square)](https://travis-ci.org/laravel-doctrine/fluent)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/laravel-doctrine/fluent.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-doctrine/fluent/)
[![StyleCI](https://styleci.io/repos/42745661/shield)](https://styleci.io/repos/42745661)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/laravel-doctrine/fluent.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-doctrine/fluent/)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/ce1a40e0-5478-4221-bc18-95b147d27ef2.svg?style=flat-square)](https://insight.sensiolabs.com/projects/ce1a40e0-5478-4221-bc18-95b147d27ef2)
[![Packagist](https://img.shields.io/packagist/dt/laravel-doctrine/fluent.svg?style=flat-square)](https://packagist.org/packages/laravel-doctrine/fluent)

This mapping driver allows you to manage your mappings in an Object Oriented approach, by implementing FluentMapping and EmbeddableMapping interfaces.

The package also provides a fluent Builder over Doctrine's ClassMetadataBuilder, aimed at easing usage of Doctrine's mapping concepts in Laravel.

## Standalone usage

You need to set the `LaravelDoctrine\Fluent\FluentDriver` as your MappingDriver implementation in your EntityManager

```
$configuration = new Configuration();
$driver = new FluentDriver([
    'optionally/your/paths'
]);
$configuration->setMetadataDriverImpl($driver);
```

Then add as many mapping files as you need:

```
$driver->addMapping(new UserMapping);
$driver->addMapping(new RoleMapping);
$driver->addMapping(new FooMapping);
$driver->addMapping(new FooMapping);
```
