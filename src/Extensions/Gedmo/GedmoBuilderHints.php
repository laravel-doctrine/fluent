<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

/**
 * Trait only meant for type hinting
 *
 * @method void             loggable(string $logEntry = null)
 * @method Softdeleteable   softDelete(string $fieldName = 'deletedAt', string $type = 'dateTime')
 * @method void             timestamps(string $createdAt = 'createdAt', string $updatedAt = 'updatedAt', string $type = 'dateTime')
 * @method TranslationClass translationClass(string $class)
 * @method Tree             tree(string $strategy = 'nested', $callback = null, $autoComplete = false)
 * @method Tree             nestedSet($callback = null)
 * @method Uploadable       uploadable()
 */
trait GedmoBuilderHints
{
}