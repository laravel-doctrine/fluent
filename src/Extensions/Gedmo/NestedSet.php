<?php

namespace LaravelDoctrine\Fluent\Extensions\Gedmo;

use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Extensions\Extension;

class NestedSet extends TreeStrategy implements Buildable, Extension, Delay
{
    const MACRO_METHOD = 'nestedSet';

    /**
     * @var string
     */
    protected $left;

    /**
     * @var string
     */
    protected $right;

    /**
     * @var string
     */
    protected $root;

    public static function enable()
    {
        parent::enable();

        TreeLeft::enable();
        TreeRight::enable();
        TreeSelfReference::enableRoot();
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @throws InvalidMappingException
     *
     * @return $this
     */
    public function left($field = 'left', $type = 'integer', callable $callback = null)
    {
        $this->validateNumericField($type, $field);

        $this->mapField($type, $field, $callback);

        $this->left = $field;

        return $this;
    }

    /**
     * @param string        $field
     * @param string        $type
     * @param callable|null $callback
     *
     * @throws InvalidMappingException
     *
     * @return $this
     */
    public function right($field = 'right', $type = 'integer', callable $callback = null)
    {
        $this->validateNumericField($type, $field);

        $this->mapField($type, $field, $callback);

        $this->right = $field;

        return $this;
    }

    /**
     * @param string        $field
     * @param callable|null $callback
     *
     * @return $this
     */
    public function root($field = 'root', callable $callback = null)
    {
        $this->addSelfReferencingRelation($field, $callback);

        $this->root = $field;

        return $this;
    }

    /**
     * Execute the build process.
     */
    public function build()
    {
        $this->builder->entity()->setRepositoryClass(NestedTreeRepository::class);

        parent::build();
    }

    /**
     * Add default values to all required fields.
     *
     * @return void
     */
    protected function defaults()
    {
        parent::defaults();

        if ($this->isMissingLeft()) {
            $this->left();
        }

        if ($this->isMissingRight()) {
            $this->right();
        }
    }

    protected function getValues()
    {
        return array_merge(parent::getValues(), [
            'strategy' => 'nested',
            'left'     => $this->left,
            'right'    => $this->right,
            'root'     => $this->root,
        ]);
    }

    /**
     * @return bool
     */
    private function isMissingLeft()
    {
        return !$this->alreadyConfigured('left') && !$this->left;
    }

    /**
     * @return bool
     */
    private function isMissingRight()
    {
        return !$this->alreadyConfigured('right') && !$this->right;
    }
}
