<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;

class GeneratedValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FieldBuilder
     */
    protected $field;

    /**
     * @var GeneratedValue
     */
    protected $fluent;

    protected function setUp()
    {
        $this->field  = $this->getMockBuilder(FieldBuilder::class)->disableOriginalConstructor()->getMock();
        $this->fluent = new GeneratedValue($this->field);
    }
    public function test_has_an_optional_strategy_that_defaults_to_auto()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');

        $this->fluent->build();
    }

    public function test_strategy_can_be_overriden_on_construction()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');

        (new GeneratedValue($this->field, 'SEQUENCE'))->build();
    }

    public function test_strategy_can_be_set_afterwards_as_well()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('IDENTITY');

        $this->fluent->strategy('IDENTITY')->build();
    }

    public function test_can_change_the_sequence_name()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 10, 1
        );

        $this->fluent->name('crazy_name')->build();
    }
    public function test_can_change_the_sequence_name_and_alloc_size()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 42, 1
        );

        $this->fluent
            ->name('crazy_name')
            ->allocationSize(42)
            ->build();
    }
    public function test_can_change_the_sequence_name_and_initial_value()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 10, 23
        );

        $this->fluent
            ->name('crazy_name')
            ->initialValue(23)
            ->build();
    }
    public function test_can_change_the_sequence_name_alloc_size_and_initial_value()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 42, 23
        );

        $this->fluent
            ->name('crazy_name')
            ->allocationSize(42)
            ->initialValue(23)
            ->build();
    }

    public function test_changing_the_alloc_size_or_initial_value_without_setting_a_name_will_generate_a_random_name()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            $this->anything(), 42, 23
        );

        $this->fluent
            ->allocationSize(42)
            ->initialValue(23)
            ->build();
    }
}
