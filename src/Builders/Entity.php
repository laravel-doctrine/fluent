<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class Entity extends AbstractBuilder
{
    /**
     * @param string $class
     *
     * @return Entity
     */
    public function setRepositoryClass($class)
    {
        $this->builder->setCustomRepositoryClass($class);

        return $this;
    }

    /**
     * @return Entity
     */
    public function readOnly()
    {
        $this->builder->setReadOnly();

        return $this;
    }

    /**
     * Enables second-level cache on this entity.
     * If you want to enable second-level cache,
     * you must enable it on the EntityManager configuration.
     * Depending on the cache mode selected, you may also need to configure
     * lock modes.
     *
     * @param int         $usage  Cache mode. use ClassMetadataInfo::CACHE_USAGE_* constants.
     *                            Defaults to READ_ONLY mode.
     * @param string|null $region The cache region to be used. Doctrine will use a default region
     *                            for each entity, if none is provided.
     *
     * @return Entity
     * @see http://doctrine-orm.readthedocs.org/en/latest/reference/second-level-cache.html
     */
    public function cacheable($usage = ClassMetadataInfo::CACHE_USAGE_READ_ONLY, $region = null)
    {
        $meta = $this->builder->getClassMetadata();
        $meta->enableCache(compact('usage', $region === null ?: 'region'));

        return $this;
    }
}
