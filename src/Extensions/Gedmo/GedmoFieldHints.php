<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

/**
 * Trait only meant for type hinting.
 *
 * @method Blameable      blameable()
 * @method IpTraceable    ipTraceable()
 * @method Sluggable      sluggable(mixed $fields)
 * @method SoftDeleteable softDelete()
 * @method void           sortableGroup()
 * @method void           sortablePosition()
 * @method Timestampable  timestampable()
 * @method void           translatable()
 * @method void           treeLeft()
 * @method void           treeLevel()
 * @method void           treeParent()
 * @method TreePath       treePath($separator = '|', callable $callback = null)
 * @method void           treePathHash()
 * @method void           treePathSource()
 * @method void           treeRight()
 * @method void           treeRoot()
 * @method void           asFileMimeType()
 * @method void           asFileName()
 * @method void           asFilePath()
 * @method void           asFileSize()
 * @method void           versioned()
 */
trait GedmoFieldHints
{
}
