<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Uploadable\FilenameGenerator\FilenameGeneratorInterface;
use Gedmo\Uploadable\Mapping\Driver\Fluent;
use Gedmo\Uploadable\Mapping\Validator;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Uploadable;
use PHPUnit\Framework\TestCase;

class UploadableTest extends TestCase
{
    /**
     * @var Uploadable
     */
    private $builder;

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    protected function setUp(): void
    {
        $this->classMetadata = new ExtensibleClassMetadata(UploadableClass::class);
        $this->classMetadata->wakeupReflection(new RuntimeReflectionService());

        Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'foo')->build();

        $this->builder = new Uploadable($this->classMetadata);
    }

    public function test_it_should_add_itself_as_a_builder_macro()
    {
        Uploadable::enable();

        $builder = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);

        $this->assertInstanceOf(
            Uploadable::class,
            call_user_func([$builder, Uploadable::MACRO_METHOD])
        );
    }

    public function test_it_should_add_uploadable_file_as_a_field_macro()
    {
        Uploadable::enable();

        $builder = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);

        $builder->uploadable();
        $builder->string('fooes')->asFileName();
        $builder->string('bar')->asFilePath();
        $builder->decimal('baz')->asFileSize();
        $builder->string('barbaz')->asFileMimeType();

        $builder->build();

        $this->assertExtensionConfiguration([
            'fileNameField'     => 'fooes',
            'filePathField'     => 'bar',
            'fileSizeField'     => 'baz',
            'fileMimeTypeField' => 'barbaz',
        ]);
    }

    public function test_it_should_have_all_default_fields()
    {
        $this->workingBuilder()->build();

        $this->assertExtensionConfiguration();
    }

    public function test_it_may_allow_file_overwrites()
    {
        $this->workingBuilder()->allowOverwrite()->build();

        $this->assertExtensionConfiguration(['allowOverwrite' => true]);
    }

    public function test_it_may_append_a_number_if_exists()
    {
        $this->workingBuilder()->appendNumber()->build();

        $this->assertExtensionConfiguration(['appendNumber' => true]);
    }

    public function test_it_may_have_a_custom_path_mapped_to_it()
    {
        $this->workingBuilder()->path('/custom/path')->build();

        $this->assertExtensionConfiguration(['path' => '/custom/path']);
    }

    public function test_it_may_call_a_method_on_the_entity_to_load_the_path()
    {
        $this->workingBuilder()->pathMethod('getPath')->build();

        $this->assertExtensionConfiguration(['pathMethod' => 'getPath']);
    }

    public function test_it_may_bind_a_callback_after_move()
    {
        $this->workingBuilder()->callback('callThis')->build();

        $this->assertExtensionConfiguration(['callback' => 'callThis']);
    }

    public function test_it_may_have_an_alphanumeric_filename_generator_mapped()
    {
        $this->workingBuilder()->alphanumericFilename()->build();

        $this->assertExtensionConfiguration([
            'filenameGenerator' => Validator::FILENAME_GENERATOR_ALPHANUMERIC,
        ]);
    }

    public function test_it_may_have_sha1_filename_generator_mapped()
    {
        $this->workingBuilder()->sha1Filename()->build();

        $this->assertExtensionConfiguration([
            'filenameGenerator' => Validator::FILENAME_GENERATOR_SHA1,
        ]);
    }

    public function test_it_may_have_custom_filename_generator_mapped()
    {
        $this->workingBuilder()->customFilename(CustomFilenameGenerator::class)->build();

        $this->assertExtensionConfiguration([
            'filenameGenerator' => CustomFilenameGenerator::class,
        ]);
    }

    public function test_it_should_have_a_max_size_restriction()
    {
        $this->workingBuilder()->maxSize(1337)->build();

        $this->assertExtensionConfiguration([
            'maxSize' => 1337,
        ]);
    }

    public function test_it_should_allow_a_specific_type()
    {
        $this->workingBuilder()->allow('jpg')->build();

        $this->assertExtensionConfiguration([
            'allowedTypes' => ['jpg'],
        ]);
    }

    public function test_it_should_allow_multiple_types_as_variadic_params()
    {
        $this->workingBuilder()->allow('jpg', 'png', 'gif')->build();

        $this->assertExtensionConfiguration([
            'allowedTypes' => ['jpg', 'png', 'gif'],
        ]);
    }

    public function test_it_should_allow_multiple_types_as_a_single_array_param()
    {
        $this->workingBuilder()->allow(['jpg', 'png', 'gif'])->build();

        $this->assertExtensionConfiguration([
            'allowedTypes' => ['jpg', 'png', 'gif'],
        ]);
    }

    public function test_it_should_disallow_a_specific_type()
    {
        $this->workingBuilder()->disallow('jpg')->build();

        $this->assertExtensionConfiguration([
            'disallowedTypes' => ['jpg'],
        ]);
    }

    public function test_it_should_disallow_multiple_types_as_variadic_params()
    {
        $this->workingBuilder()->disallow('jpg', 'png', 'gif')->build();

        $this->assertExtensionConfiguration([
            'disallowedTypes' => ['jpg', 'png', 'gif'],
        ]);
    }

    public function test_it_should_disallow_multiple_types_as_a_single_array_param()
    {
        $this->workingBuilder()->disallow(['jpg', 'png', 'gif'])->build();

        $this->assertExtensionConfiguration([
            'disallowedTypes' => ['jpg', 'png', 'gif'],
        ]);
    }

    public function test_it_shouldnt_allow_and_disallow_at_the_same_time()
    {
        $this->expectException(InvalidMappingException::class);

    	$this->workingBuilder()->allow('jpg')->disallow('doc')->build();
    }

    public function test_it_needs_a_field_set_up_as_path_or_name()
    {
        $this->expectException(InvalidMappingException::class);

        $this->builder->build();
    }

    public function test_it_validates_method_exists()
    {
        $this->expectException(InvalidMappingException::class);

        $this->workingBuilder()->pathMethod('nonExistent')->build();
    }

    public function test_it_validates_callback_exists()
    {
        $this->expectException(InvalidMappingException::class);

        $this->workingBuilder()->callback('nonExistent')->build();
    }

    public function test_it_validates_positive_sizes()
    {
        $this->expectException(InvalidMappingException::class);

        $this->workingBuilder()->maxSize(-1)->build();
    }

    /**
     * @dataProvider getTypesWithoutString
     */
    public function test_it_validates_that_file_path_field_is_mapped_as_a_string($type)
    {
        $this->expectException(InvalidMappingException::class);

        Uploadable::enable();

        $fluent = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);
        $fluent->uploadable();
        $fluent->field($type, 'bar')->asFilePath();
        $fluent->build();
    }

    /**
     * @dataProvider getTypesWithoutString
     */
    public function test_it_validates_that_file_name_field_is_mapped_as_a_string($type)
    {
        $this->expectException(InvalidMappingException::class);

        Uploadable::enable();

        $fluent = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);
        $fluent->uploadable();
        $fluent->field($type, 'bar')->asFileName();
        $fluent->build();
    }

    /**
     * @dataProvider getTypesWithoutString
     */
    public function test_it_validates_that_file_mime_type_field_is_mapped_as_a_string($type)
    {
        $this->expectException(InvalidMappingException::class);

        Uploadable::enable();

        $fluent = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);
        $fluent->uploadable();
        $fluent->field($type, 'bar')->asFileMimeType();
        $fluent->build();
    }

    /**
     * @dataProvider getTypesWithoutDecimal
     */
    public function test_it_validates_that_file_mime_type_field_is_mapped_as_a_decimal($type)
    {
        $this->expectException(InvalidMappingException::class);

        Uploadable::enable();

        $fluent = new Builder(new ClassMetadataBuilder($this->classMetadata), new DefaultNamingStrategy);
        $fluent->uploadable();
        $fluent->field($type, 'bar')->asFileSize();
        $fluent->build();
    }

    private function assertExtensionConfiguration(array $config = [])
    {
        $this->assertEquals($this->defaults($config), $this->classMetadata->getExtension(Fluent::EXTENSION_NAME));
    }

    /**
     * @param array $overrides
     *
     * @return array
     */
    private function defaults(array $overrides = [])
    {
        return array_merge([
            'uploadable'        => true,
            'allowOverwrite'    => false,
            'appendNumber'      => false,
            'path'              => '',
            'pathMethod'        => '',
            'callback'          => '',
            'fileMimeTypeField' => false,
            'fileNameField'     => 'foo',
            'filePathField'     => false,
            'fileSizeField'     => false,
            'filenameGenerator' => Validator::FILENAME_GENERATOR_NONE,
            'maxSize'           => (double)0,
            'allowedTypes'      => false,
            'disallowedTypes'   => false,
        ], $overrides);
    }

    /**
     * @return Uploadable
     */
    private function workingBuilder()
    {
        $this->classMetadata->addExtension(Fluent::EXTENSION_NAME, [
            'fileNameField' => 'foo',
        ]);

        return $this->builder;
    }

    public function getTypesWithoutString()
    {
        return $this->getTypesExcept('string');
    }

    public function getTypesWithoutDecimal()
    {
        return $this->getTypesExcept('decimal');
    }

    private function getTypesExcept($type)
    {
        $types = Type::getTypesMap();
        unset($types[$type]);

        return array_map(function($type){
            return [$type];
        }, array_keys($types));
    }
}

class UploadableClass
{
    private $foo;

    public function getPath()
    {
    }

    public function callThis()
    {
    }
}

class CustomFilenameGenerator implements FilenameGeneratorInterface
{
    public static function generate($filename, $extension, $object = null)
    {
    }
}
