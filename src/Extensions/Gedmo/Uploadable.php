<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Uploadable\Mapping\Driver\Fluent as UploadableDriver;
use Gedmo\Uploadable\Mapping\Validator;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Extension;

class Uploadable implements Buildable, Delay, Extension
{
    const MACRO_METHOD = 'uploadable';

    /**
     * @var bool
     */
    private $allowOverwrite = false;

    /**
     * @var bool
     */
    private $appendNumber = false;

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $pathMethod = '';

    /**
     * @var string
     */
    private $callback = '';

    /**
     * @var string
     */
    private $filenameGenerator = Validator::FILENAME_GENERATOR_NONE;

    /**
     * @var float
     */
    private $maxSize = 0;

    /**
     * @var string
     */
    private $allowedTypes = '';

    /**
     * @var string
     */
    private $disallowedTypes = '';

    /**
     * @var ExtensibleClassMetadata
     */
    private $classMetadata;

    /**
     * @param ExtensibleClassMetadata $classMetadata
     */
    public function __construct(ExtensibleClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }

    /**
     * Enable the Uploadable extension.
     *
     * @return void
     */
    public static function enable()
    {
        Builder::macro(self::MACRO_METHOD, function (Builder $builder) {
            return new static($builder->getClassMetadata());
        });

        UploadableFile::enable();
    }

    /**
     * If this option is true, it will overwrite a file if it already exists. If you set "false",
     * an exception will be thrown.
     *
     * Default: false
     *
     * @return Uploadable
     */
    public function allowOverwrite()
    {
        $this->allowOverwrite = true;

        return $this;
    }

    /**
     * If the file already exists and "allowOverwrite" is false, append a number to the filename.
     *
     * Example: if you're uploading a file named "test.txt", if the file already exists and this option
     * is true, the extension will modify the name of the uploaded file to "test-1.txt", where "1" could
     * be any number. The extension will check if the file exists until it finds a filename with a number
     * as its postfix that is not used. If you use a filename generator and this option is true, it will
     * append a number to the filename anyway if a file with the same name already exists.
     *
     * Default value: false
     *
     * @return Uploadable
     */
    public function appendNumber()
    {
        $this->appendNumber = true;

        return $this;
    }

    /**
     * The path where the files represented by this entity will be moved. Path can be set in other ways:
     * From the listener or from a method.
     *
     * Default: "".
     *
     * @param string $path
     *
     * @return Uploadable
     */
    public function path($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Similar to "path", this represents the name of a method on the entity that will return the path
     * where the files represented by this entity will be moved. This is useful in several cases. For
     * example, you can set specific paths for specific entities, or you can get the path from other
     * sources (like a framework configuration) instead of hard-coding it in the entity.
     *
     * As first argument this method takes default path, so you can return a path relative to the default.
     *
     * Default: "".
     *
     * @param string $methodName
     *
     * @return Uploadable
     */
    public function pathMethod($methodName)
    {
        $this->pathMethod = $methodName;

        return $this;
    }

    /**
     * Allows you to set a method name. If set, the method will be called after the file is moved.
     *
     * This method will receive an array with information about the uploaded file, which includes the
     * following keys:
     *
     * <ul>
     * <li>fileName: The filename.</li>
     * <li>fileExtension: The extension of the file (including the dot). Example: .jpg</li>
     * <li>fileWithoutExt: The filename without the extension.</li>
     * <li>filePath: The file path. Example: /my/path/filename.jpg</li>
     * <li>fileMimeType: The mime-type of the file. Example: text/plain.</li>
     * <li>fileSize: Size of the file in bytes. Example: 140000.</li>
     * </ul>
     *
     * Default value: "".
     *
     * @param string $callback
     *
     * @return Uploadable
     */
    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Normalizes the filename, leaving only alphanumeric characters in the filename, and replacing
     * anything else with a "-".
     *
     * @return Uploadable
     */
    public function alphanumericFilename()
    {
        $this->filenameGenerator = Validator::FILENAME_GENERATOR_ALPHANUMERIC;

        return $this;
    }

    /**
     * Generates a sha1 filename for the file.
     *
     * @return Uploadable
     */
    public function sha1Filename()
    {
        $this->filenameGenerator = Validator::FILENAME_GENERATOR_SHA1;

        return $this;
    }

    /**
     * Set a custom FilenameGenerator class. This class must implement
     * `Gedmo\Uploadable\FilenameGenerator\FilenameGeneratorInterface`.
     *
     * @param string $className
     *
     * @return Uploadable
     */
    public function customFilename($className)
    {
        $this->filenameGenerator = $className;

        return $this;
    }

    /**
     * Set a maximum size for the file, in bytes. If file size exceeds the value set in this
     * configuration, `UploadableMaxSizeException` will be thrown.
     *
     * Default value: 0, meaning that no size validation will occur.
     *
     * @param float $bytes
     *
     * @return Uploadable
     */
    public function maxSize($bytes)
    {
        $this->maxSize = $bytes;

        return $this;
    }

    /**
     * Allow only specific types.
     *
     * @param array|string ...$type can be an array or multiple string parameters
     *
     * @return Uploadable
     */
    public function allow($type)
    {
        if (!is_array($type)) {
            $type = func_get_args();
        }
        $this->allowedTypes = implode(',', $type);

        return $this;
    }

    /**
     * Disallow specific types.
     *
     * @param array|string ...$type can be an array or multiple string parameters
     *
     * @return Uploadable
     */
    public function disallow($type)
    {
        if (!is_array($type)) {
            $type = func_get_args();
        }
        $this->disallowedTypes = implode(',', $type);

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $config = $this->getConfiguration();

        Validator::validateConfiguration($this->classMetadata, $config);

        $this->classMetadata->addExtension(UploadableDriver::EXTENSION_NAME, $config);
    }

    /**
     * Build the configuration, based on defaults, current extension configuration and accumulated parameters.
     *
     * @return array
     */
    private function getConfiguration()
    {
        return array_merge(
            [
                'fileMimeTypeField' => false,
                'fileNameField'     => false,
                'filePathField'     => false,
                'fileSizeField'     => false,
            ],
            $this->classMetadata->getExtension(UploadableDriver::EXTENSION_NAME),
            [
                'uploadable'        => true,
                'allowOverwrite'    => $this->allowOverwrite,
                'appendNumber'      => $this->appendNumber,
                'path'              => $this->path,
                'pathMethod'        => $this->pathMethod,
                'callback'          => $this->callback,
                'filenameGenerator' => $this->filenameGenerator,
                'maxSize'           => (float) $this->maxSize,
                'allowedTypes'      => $this->allowedTypes,
                'disallowedTypes'   => $this->disallowedTypes,
            ]
        );
    }
}
