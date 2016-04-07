<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use Tests\Builders\IsMacroable;
use Tests\Relations\Traits\Indexable;
use Tests\Relations\Traits\NonPrimary;
use Tests\Relations\Traits\Orderable;
use Tests\Relations\Traits\Ownable;
use Tests\Relations\Traits\Owning;

class ManyToManyTest extends RelationTestCase
{
    use Indexable, Orderable, Owning, Ownable, NonPrimary, IsMacroable;

    /**
     * @var ManyToMany
     */
    protected $relation;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $field = 'children';

    protected function setUp()
    {
        $this->builder = new ClassMetadataBuilder(new ClassMetadataInfo(
            FluentEntity::class
        ));

        $this->relation = new ManyToMany($this->builder, new DefaultNamingStrategy(), $this->field,
            FluentEntity::class);
    }

    public function test_can_set_join_table()
    {
        $this->relation->joinTable('other_table');

        $this->relation->build();

        $this->assertEquals('other_table', $this->getAssocValue($this->field, 'joinTable')['name']);
    }

    public function test_can_set_join_column()
    {
        $this->relation->joinColumn('join_column', 'other_reference');

        $this->relation->build();

        $this->assertEquals('join_column', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_unique_join_column()
    {
        $this->relation->joinColumn('join_column', 'other_reference', true);

        $this->relation->build();

        $this->assertEquals('join_column', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertTrue($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_foreign_key()
    {
        $this->relation->foreignKey('foreign_key', 'other_reference');

        $this->relation->build();

        $this->assertEquals('foreign_key', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_unique_foreign_key()
    {
        $this->relation->foreignKey('foreign_key', 'other_reference', true);

        $this->relation->build();

        $this->assertEquals('foreign_key', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertTrue($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_source()
    {
        $this->relation->source('source', 'other_reference');

        $this->relation->build();

        $this->assertEquals('source', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_unique_source()
    {
        $this->relation->source('source', 'other_reference', true);

        $this->relation->build();

        $this->assertEquals('source', $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['nullable']);
        $this->assertTrue($this->getAssocValue($this->field, 'joinTable')['joinColumns'][0]['unique']);
    }

    public function test_can_set_inverseKey()
    {
        $this->relation->inverseKey('inverse_key', 'other_reference');

        $this->relation->build();

        $this->assertEquals('inverse_key',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['nullable']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['unique']);
    }

    public function test_can_set_unique_inverseKey()
    {
        $this->relation->inverseKey('inverse_key', 'other_reference', true);

        $this->relation->build();

        $this->assertEquals('inverse_key',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['nullable']);
        $this->assertTrue($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['unique']);
    }

    public function test_can_set_target()
    {
        $this->relation->target('target', 'other_reference');

        $this->relation->build();

        $this->assertEquals('target', $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['nullable']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['unique']);
    }

    public function test_can_set_unique_target()
    {
        $this->relation->target('target', 'other_reference', true);

        $this->relation->build();

        $this->assertEquals('target', $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['name']);
        $this->assertEquals('other_reference',
            $this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['referencedColumnName']);
        $this->assertFalse($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['nullable']);
        $this->assertTrue($this->getAssocValue($this->field, 'joinTable')['inverseJoinColumns'][0]['unique']);
    }

    public function test_can_add_join_column()
    {
        $this->relation->addJoinColumn('children2');

        $this->relation->build();

        $assoc = $this->getAssocValue($this->field, 'joinTable')['joinColumns'][0];

        $this->assertEquals('children2_id', $assoc['name']);
        $this->assertEquals('id', $assoc['referencedColumnName']);
        $this->assertFalse($assoc['nullable']);
    }

    public function test_can_get_join_columns()
    {
        $this->assertCount(0, $this->relation->getJoinColumns());

        $this->relation->addJoinColumn('children');

        $this->assertCount(1, $this->relation->getJoinColumns());
    }

    /**
     * Get the builder under test.
     *
     * @return Macroable
     */
    protected function getMacroableBuilder()
    {
        return $this->relation;
    }
}
