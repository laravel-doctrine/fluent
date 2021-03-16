<?php
namespace Tests\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;

/**
 * @mixin TestCase
 */
trait TrackingExtensions
{
    /**
     * @var string
     */
    protected $fieldName = 'someField';

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;


    /**
     * @return AbstractTrackingExtension
     */
    abstract protected function getExtension();
    
    /**
     * @return string
     */
    abstract protected function getExtensionName();
    
    public function test_it_should_fail_without_binding_which_event_to_use()
    {
        $this->expectException(InvalidMappingException::class);

        $this->getExtension()->build();
    }

    public function test_it_should_allow_blaming_on_create()
    {
        $this->getExtension()->onCreate()->build();

        $this->assertBuildResultIs([
            'create' => [
                $this->fieldName,
            ],
        ]);
    }

    public function test_it_should_allow_blaming_on_updates()
    {
        $this->getExtension()->onUpdate()->build();

        $this->assertBuildResultIs([
            'update' => [
                $this->fieldName,
            ],
        ]);
    }

    public function test_it_should_allow_blaming_on_specific_changes()
    {
        $this->getExtension()->onChange()->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => null,
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_a_specific_field_for_changes()
    {
        $this->getExtension()->onChange('foobar')->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => 'foobar',
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_a_specific_field_with_a_specific_value_for_changes()
    {
        $this->getExtension()->onChange('foobar', 'Published')->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => 'foobar',
                    'value'        => 'Published',
                ],
            ],
        ]);
    }

    public function test_it_should_allow_tracking_an_array_of_fields_for_changes()
    {
        $this->getExtension()->onChange(['foobar', 'baz'])->build();

        $this->assertBuildResultIs([
            'change' => [
                [
                    'field'        => $this->fieldName,
                    'trackedField' => ['foobar', 'baz'],
                    'value'        => null,
                ],
            ],
        ]);
    }

    public function test_it_should_not_allow_tracking_an_array_of_fields_with_a_specific_value_for_changes()
    {
        $this->expectException(InvalidMappingException::class);

        $this->getExtension()->onChange(['foobar', 'baz'], 'this_will_explode')->build();
    }

    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertBuildResultIs(array $expected)
    {
        $this->assertEquals($expected, $this->classMetadata->getExtension(
            $this->getExtensionName()
        ));
    }
}
