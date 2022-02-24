<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Extensions\Gedmo\GedmoBuilderHints;
use LaravelDoctrine\Fluent\Fluent;
use LogicException;

/**
 * @method Field array($name, callable $callback = null)
 */
class Builder extends AbstractBuilder implements Fluent
{
    use Traits\Fields;
    use Traits\Dates;
    use Traits\Aliases;
    use Traits\Relations;
    use Traits\Constraints;
    use Traits\Macroable;
    use Traits\Queueable;
    use Traits\QueuesMacros;
    use GedmoBuilderHints;

    /**
     * {@inheritdoc}
     */
    public function table($name, callable $callback = null)
    {
        $this->disallowInEmbeddedClasses();

        $table = new Table($this->builder, $name);

        $this->callbackAndQueue($table, $callback);

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function entity(callable $callback = null)
    {
        $this->disallowInEmbeddedClasses();

        $entity = new Entity($this->builder, $this->namingStrategy);

        $this->callIfCallable($callback, $entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function inheritance($type, callable $callback = null)
    {
        $inheritance = Inheritance\InheritanceFactory::create($type, $this->builder);

        $this->callIfCallable($callback, $inheritance);

        return $inheritance;
    }

    /**
     * {@inheritdoc}
     */
    public function singleTableInheritance(callable $callback = null)
    {
        return $this->inheritance(Inheritance\Inheritance::SINGLE, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function joinedTableInheritance(callable $callback = null)
    {
        return $this->inheritance(Inheritance\Inheritance::JOINED, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function embed($embeddable, $field = null, callable $callback = null)
    {
        $embedded = new Embedded(
            $this->builder,
            $this->namingStrategy,
            $this->guessSingularField($embeddable, $field),
            $embeddable
        );

        $this->callbackAndQueue($embedded, $callback);

        return $embedded;
    }

    /**
     * {@inheritdoc}
     */
    public function override($name, callable $callback)
    {
        $override = new Overrides\Override(
            $this->getBuilder(),
            $this->getNamingStrategy(),
            $name,
            $callback
        );

        $this->queue($override);

        return $override;
    }

    /**
     * {@inheritdoc}
     */
    public function events(callable $callback = null)
    {
        $events = new LifecycleEvents($this->builder);

        $this->callbackAndQueue($events, $callback);

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function listen(callable $callback = null)
    {
        $events = new EntityListeners($this->builder);

        $this->callbackAndQueue($events, $callback);

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmbeddedClass()
    {
        return $this->builder->getClassMetadata()->isEmbeddedClass;
    }

    /**
     * @param string        $name
     * @param callable|null $callback
     *
     * @return Field
     */
    protected function setArray($name, callable $callback = null)
    {
        return $this->field(Types::ARRAY, $name, $callback);
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        // Workaround for reserved keywords
        if ($method === 'array') {
            return call_user_func_array([$this, 'setArray'], $params);
        }

        if ($this->hasMacro($method)) {
            return $this->queueMacro($method, $params);
        }

        throw new InvalidArgumentException('Fluent builder method ['.$method.'] does not exist');
    }

    /**
     * {@inheritdoc}
     */
    protected function disallowInEmbeddedClasses($message = '')
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException($message);
        }
    }
}
