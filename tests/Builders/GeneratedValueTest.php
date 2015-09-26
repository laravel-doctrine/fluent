<?php

namespace Tests\Builders;

use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\GeneratedValue;

class GeneratedValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FieldBuilder
     */
    protected $field;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ClassMetadataInfo
     */
    protected $cm;

    /**
     * @var GeneratedValue
     */
    protected $fluent;

    protected function setUp()
    {
        $this->field = $this->getMockBuilder(FieldBuilder::class)->disableOriginalConstructor()->getMock();
        $this->cm    = $this->getMockBuilder(ClassMetadataInfo::class)->disableOriginalConstructor()->getMock();

        $this->fluent = new GeneratedValue($this->field, $this->cm);
    }

    public function test_has_an_optional_strategy_that_defaults_to_auto()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');

        $this->fluent->build();
    }

    public function test_auto_strategy_can_be_set_afterwards_as_well()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');

        $this->fluent->auto()->build();
    }

    public function test_identity_strategy_can_be_set_afterwards()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('IDENTITY');

        $this->fluent->identity()->build();
    }

    public function test_uuid_strategy_can_be_set_afterwards()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('UUID');

        $this->fluent->uuid()->build();
    }

    public function test_none_strategy_can_be_set_afterwards()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('NONE');

        $this->fluent->none()->build();
    }

    public function test_a_custom_strategy_with_the_implementation_can_be_set_afterwards()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('CUSTOM');
        $this->cm->expects($this->once())->method('setCustomGeneratorDefinition')->with(
            ['class' => 'A\\Class\\Implementing\\AbstractIdGenerator']
        );

        $this->fluent->custom('A\\Class\\Implementing\\AbstractIdGenerator')->build();
    }

    public function test_can_change_the_sequence_name_on_auto_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', $this->anything(), $this->anything()
        );

        $this->fluent->auto('crazy_name')->build();
    }

    public function test_can_change_the_sequence_name_and_alloc_size_on_auto_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', $this->anything(), 42
        );

        $this->fluent
            ->auto('crazy_name', 42)
            ->build();
    }

    public function test_can_change_the_sequence_name_and_initial_value_on_auto_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 23, $this->anything()
        );

        $this->fluent
            ->auto('crazy_name', null, 23)
            ->build();
    }

    public function test_can_change_the_sequence_name_alloc_size_and_initial_value_on_auto_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 23, 42
        );

        $this->fluent
            ->auto('crazy_name', 42, 23)
            ->build();
    }

    public function test_changing_the_alloc_size_or_initial_value_in_auto_without_setting_a_name_will_get_ignored()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('AUTO');
        $this->field->expects($this->never())->method('setSequenceGenerator');

        $this->fluent
            ->auto(null, 42, 23)
            ->build();
    }

    public function test_can_change_the_sequence_name_on_sequence_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', $this->anything(), $this->anything()
        );

        $this->fluent->sequence('crazy_name')->build();
    }

    public function test_can_change_the_sequence_name_and_alloc_size_on_sequence_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', $this->anything(), 42
        );

        $this->fluent
            ->sequence('crazy_name', 42)
            ->build();
    }

    public function test_can_change_the_sequence_name_and_initial_value_on_sequence_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 23, $this->anything()
        );

        $this->fluent
            ->sequence('crazy_name', null, 23)
            ->build();
    }

    public function test_can_change_the_sequence_name_alloc_size_and_initial_value_on_sequence_strategy()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');
        $this->field->expects($this->once())->method('setSequenceGenerator')->with(
            'crazy_name', 23, 42
        );

        $this->fluent
            ->sequence('crazy_name', 42, 23)
            ->build();
    }

    public function test_changing_the_alloc_size_or_initial_value_in_sequence_without_setting_a_name_will_get_ignored()
    {
        $this->field->expects($this->once())->method('generatedValue')->with('SEQUENCE');
        $this->field->expects($this->never())->method('setSequenceGenerator');

        $this->fluent
            ->sequence(null, 42, 23)
            ->build();
    }
}
