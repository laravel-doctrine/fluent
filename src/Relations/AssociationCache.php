<?php

namespace LaravelDoctrine\Fluent\Relations;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;

class AssociationCache implements Buildable
{
    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $usage;

    /**
     * @var array
     */
    protected $usages = [
        'READ_ONLY'            => ClassMetadataInfo::CACHE_USAGE_READ_ONLY,
        'NONSTRICT_READ_WRITE' => ClassMetadataInfo::CACHE_USAGE_NONSTRICT_READ_WRITE,
        'READ_WRITE'           => ClassMetadataInfo::CACHE_USAGE_READ_WRITE,
    ];

    /**
     * @var string
     */
    protected $field;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @param ClassMetadata $metadata
     * @param string        $field
     * @param string|int    $usage
     * @param string|null   $region
     */
    public function __construct(ClassMetadata $metadata, $field, $usage = 'READ_ONLY', $region = null)
    {
        $this->field = $field;
        $this->metadata = $metadata;
        $this->setRegion($region);
        $this->setUsage($usage);
    }

    /**
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @param string $usage
     *
     * @throws InvalidArgumentException
     *
     * @return AssociationCache
     */
    public function setUsage($usage)
    {
        if (is_int($usage)) {
            $this->validate($usage, $this->usages);
        } else {
            $this->validate($usage, array_keys($this->usages));
            $usage = $this->usages[$usage];
        }

        $this->usage = $usage;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     *
     * @return AssociationCache
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->metadata->enableAssociationCache($this->field, [
            'usage'  => $this->getUsage(),
            'region' => $this->getRegion(),
        ]);
    }

    /**
     * @param string|int $usage
     * @param array      $usages
     *
     * @return mixed
     */
    protected function validate($usage, array $usages)
    {
        if (!in_array($usage, $usages)) {
            throw new InvalidArgumentException(
                '['.$usage.'] is not a valid cache usage. Available: '.implode(', ', array_keys($this->usages))
            );
        }

        return $usage;
    }
}
