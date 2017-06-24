<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Builders\Traits\Queueable;
use LaravelDoctrine\Fluent\Extensions\Extension;

class MaterializedPath extends TreeStrategy implements Buildable, Extension, Delay
{
    use Queueable {
        build as buildQueue;
    }

    /**
     * @var null|TreePath
     */
    private $path;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var bool
     */
    private $appendIds;

    /**
     * @var bool
     */
    private $startsWithSeparator = false;

    /**
     * @var bool
     */
    private $endsWithSeparator = true;

    /**
     * {@inheritdoc}
     */
    public static function enable()
    {
        parent::enable();

        TreePath::enable();
        TreePathHash::enable();
        TreePathSource::enable();
    }

    /**
     * @param string        $field
     * @param string        $separator
     * @param callable|null $callback
     *
     * @return $this
     */
    public function path($field = 'path', $separator = '|', callable $callback = null)
    {
        $this->mapField('string', $field, null, true);

        $this->path = new TreePath($this->getClassMetadata(), $field, $separator);

        $this->callbackAndQueue($this->path, $callback);

        return $this;
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function pathHash($hash = 'pathHash')
    {
        $this->mapField('string', $hash);

        $this->hash = $hash;

        return $this;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function pathSource($field = 'id')
    {
        $this->source = $field;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        parent::build();

        $this->buildQueue();
    }

    /**
     * Add default values to all required fields.
     *
     * @return void
     */
    protected function defaults()
    {
        parent::defaults();

        if ($this->isMissingPath()) {
            $this->path();
        }

        if ($this->isMissingSource()) {
            $this->pathSource();
        }
    }

    /**
     * @return array
     */
    protected function getValues()
    {
        $values = array_merge(parent::getValues(), [
            'strategy'                   => 'materializedPath',
            'path_source'                => $this->source,
            'path_separator'             => $this->separator,
            'path_append_id'             => $this->appendIds,
            'path_starts_with_separator' => $this->startsWithSeparator,
            'path_ends_with_separator'   => $this->endsWithSeparator,
            'activate_locking'           => false,
        ]);

        if ($this->hash) {
            $values['path_hash'] = $this->hash;
        }

        return $values;
    }

    /**
     * @return bool
     */
    private function isMissingPath()
    {
        return !$this->alreadyConfigured('path') && !$this->path;
    }

    /**
     * @return bool
     */
    private function isMissingSource()
    {
        return !$this->alreadyConfigured('path_source') && !$this->source;
    }
}
