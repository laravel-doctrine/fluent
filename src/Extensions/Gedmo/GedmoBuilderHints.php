<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

/**
 * Trait only meant for type hinting.
 *
 * @method void             loggable(string $logEntry = null)
 * @method SoftDeleteable   softDelete(string $fieldName = 'deletedAt', string $type = 'dateTime')
 * @method void             timestamps(string $createdAt = 'createdAt', string $updatedAt = 'updatedAt', string $type = 'dateTime')
 * @method TranslationClass translationClass(string $class)
 * @method Tree             tree(callable $callback = null)
 * @method Uploadable       uploadable()
 * @method void             locale(string $fieldName)
 */
trait GedmoBuilderHints
{
}
