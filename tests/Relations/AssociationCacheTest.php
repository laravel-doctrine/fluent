<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\ClassMetadata;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Relations\AssociationCache;
use Tests\Stubs\Entities\StubEntity;

class AssociationCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $field = 'parent';

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    protected function setUp()
    {
        $this->metadata = new ClassMetadata(StubEntity::class);
    }

    public function test_can_cache_usage_as_string()
    {
        $cache = $this->factory('READ_ONLY');
        $this->assertEquals(1, $cache->getUsage());

        $cache = $this->factory('NONSTRICT_READ_WRITE');
        $this->assertEquals(2, $cache->getUsage());

        $cache = $this->factory('READ_WRITE');
        $this->assertEquals(3, $cache->getUsage());
    }

    public function test_can_cache_usage_as_integer()
    {
        $cache = $this->factory(1);
        $this->assertEquals(1, $cache->getUsage());

        $cache = $this->factory(2);
        $this->assertEquals(2, $cache->getUsage());

        $cache = $this->factory(3);
        $this->assertEquals(3, $cache->getUsage());
    }

    public function test_cannot_set_non_existing_cache_usages()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            '[NON_EXISTING] is not a valid cache usage. Available: READ_ONLY, NONSTRICT_READ_WRITE, READ_WRITE'
        );

        $this->factory('NON_EXISTING');
    }

    public function test_can_set_custom_region()
    {
        $cache = $this->factory('READ_ONLY', 'custom_region');

        $this->assertEquals('custom_region', $cache->getRegion());
    }

    public function test_cache_usage_gets_set_on_the_meta_data_on_build()
    {
        $cache = $this->factory(2);

        $cache->build();

        $mapping = $this->metadata->getAssociationMapping($this->field);

        $this->assertEquals(2, $mapping['cache']['usage']);
    }

    public function test_a_default_region_will_be_generated_on_build_when_region_left_empty()
    {
        $cache = $this->factory('READ_ONLY');

        $cache->build();

        $mapping = $this->metadata->getAssociationMapping($this->field);

        $this->assertEquals('tests_stubs_entities_stubentity__parent', $mapping['cache']['region']);
    }

    public function test_custom_region_gets_set_on_the_meta_data_on_build()
    {
        $cache = $this->factory('READ_ONLY', 'custom_region');

        $cache->build();

        $mapping = $this->metadata->getAssociationMapping($this->field);

        $this->assertEquals('custom_region', $mapping['cache']['region']);
    }

    /**
     * @param string $usage
     * @param null   $region
     *
     * @return AssociationCache
     */
    protected function factory($usage = 'READ_ONLY', $region = null)
    {
        return new AssociationCache(
            $this->metadata,
            $this->field,
            $usage,
            $region
        );
    }
}
