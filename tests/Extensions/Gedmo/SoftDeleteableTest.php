<?php
namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\AbstractTrackingExtension;
use Gedmo\SoftDeleteable\Mapping\Driver\Fluent as SoftDeleteableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\SoftDeleteable;

/**
 * @mixin \PHPUnit_Framework_TestCase
 */
class SoftDeleteableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var SoftDeleteable
     */
    private $extension;

    protected function setUp()
    {
        $this->fieldName     = 'deletedAt';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->extension     = new SoftDeleteable($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        SoftDeleteable::enable();

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string', $this->fieldName);

        $this->assertInstanceOf(
            SoftDeleteable::class,
            call_user_func([$field, SoftDeleteable::MACRO_METHOD])
        );
    }

    public function test_it_should_make_itself_a_builder_macro()
    {
        SoftDeleteable::enable();

        $builder = new Builder($cmb = new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')));

        $this->assertInstanceOf(
            SoftDeleteable::class,
            call_user_func([$builder, SoftDeleteable::MACRO_METHOD], 'deletedAt')
        );
    }

    public function test_it_should_add_softdelete_to_the_given_field()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'softDeleteable' => true,
            'fieldName'      => $this->fieldName,
            'timeAware'      => false
        ]);
    }

    public function test_it_should_allow_to_be_time_aware()
    {
        $this->getExtension()->timeAware()->build();

        $this->assertBuildResultIs([
            'softDeleteable' => true,
            'fieldName'      => $this->fieldName,
            'timeAware'      => true
        ]);
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

    /**
     * @return SoftDeleteable
     */
    protected function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return SoftdeleteableDriver::EXTENSION_NAME;
    }
}
