<?php

namespace Tests\Relations;

use BadMethodCallException;
use Doctrine\ORM\Mapping\ClassMetadata;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RelationTestCase extends TestCase
{
    protected $field;

    /**
     * @var Relation
     */
    protected $relation;

    public function test_can_set_cascade()
    {
        $this->relation->cascade(['persist']);

        $this->relation->build();

        $this->assertContains('persist', $this->getAssocValue($this->field, 'cascade'));
    }

    public function test_can_set_cascade_multiple()
    {
        $this->relation->cascade(['persist', 'remove']);

        $this->relation->build();

        $this->assertContains('persist', $this->getAssocValue($this->field, 'cascade'));
        $this->assertContains('remove', $this->getAssocValue($this->field, 'cascade'));
    }

    public function test_should_be_valid_cascade_action()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cascade [invalid] does not exist');

        $this->relation->cascade(['invalid']);
    }

    public function test_can_set_fetch()
    {
        $this->relation->fetch('EXTRA_LAZY');

        $this->relation->build();

        $this->assertEquals(ClassMetadata::FETCH_EXTRA_LAZY, $this->getAssocValue($this->field, 'fetch'));
    }

    public function test_should_be_valid_fetch_action()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fetch [invalid] does not exist');

        $this->relation->fetch('invalid');
    }

    public function test_can_cache_the_association()
    {
        $this->relation->cache();

        $this->relation->build();

        $cache = $this->getAssocValue($this->field, 'cache');
        $this->assertEquals(1, $cache['usage']);
        $this->assertEquals('tests_relations_fluententity__' . $this->field, $cache['region']);
    }

    public function test_can_cache_the_association_with_usage()
    {
        $this->relation->cache(3);

        $this->relation->build();

        $cache = $this->getAssocValue($this->field, 'cache');
        $this->assertEquals(3, $cache['usage']);
        $this->assertEquals('tests_relations_fluententity__' . $this->field, $cache['region']);
    }

    public function test_valid_cache_usage_should_be_given()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[invalid] is not a valid cache usage. Available: READ_ONLY, NONSTRICT_READ_WRITE, READ_WRITE');

        $this->relation->cache('invalid');
    }

    public function test_can_cache_the_association_with_custom_region()
    {
        $this->relation->cache(1, 'custom_region');

        $this->relation->build();

        $cache = $this->getAssocValue($this->field, 'cache');
        $this->assertEquals(1, $cache['usage']);
        $this->assertEquals('custom_region', $cache['region']);
    }

    public function test_can_call_association_builder_methods()
    {
        $this->relation->fetchEager();

        $this->relation->build();

        $this->assertEquals(ClassMetadata::FETCH_EAGER, $this->getAssocValue($this->field, 'fetch'));
    }

    public function test_calling_non_existing_methods_will_throw_exception()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Relation method [doSomethingWrong] does not exist.');

        $this->relation->doSomethingWrong();
    }

    protected function getAssocValue($field, $option)
    {
        return $this->relation->getBuilder()->getClassMetadata()->getAssociationMapping($field)[$option];
    }
}

class FluentEntity
{
    protected $parent, $children;
}
