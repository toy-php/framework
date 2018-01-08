<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Exceptions\Exception;
use Toy\Interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var \ArrayObject
     */
    protected $frozen;

    /**
     * @var \ArrayObject
     */
    protected $values;

    /**
     * @var boolean
     */
    protected $frozenValues;

    public function __construct(array $defaults = [], $frozenValues = true)
    {
        $this->frozen = new \ArrayObject();
        $this->values = new \ArrayObject($defaults);
        $this->frozenValues = $frozenValues;
    }

    /**
     * Проверка защищенности значения от изменений
     * @param $name
     * @throws Exception
     */
    private function checkFrozen($name)
    {
        if (!$this->frozenValues) {
            return;
        }
        if ($this->frozen->offsetExists($name)) {
            throw new Exception(
                sprintf('Параметр "%s" защищен от изменения', $name)
            );
        }
    }

    /**
     * Получить экземпляр с возможностью изменения значений
     * @return ContainerInterface
     */
    public function withoutFrozen(): ContainerInterface
    {
        if ($this->frozenValues === false) {
            return $this;
        }
        $instance = clone $this;
        $instance->frozenValues = false;
        return $instance;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetSet($name, $value)
    {
        if (!is_string($name) or !preg_match('#^[^0-9-_][a-z0-9-_]#is', $name)) {
            throw new Exception('Идентификатор значения должен быть строковым');
        }
        $this->checkFrozen($name);
        $this->values[$name] = $this->frozen[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($name)
    {
        if (!$this->values->offsetExists($name)) {
            throw new Exception('Значение не найдено');
        }
        $value = $this->values[$name];
        if (!is_object($value)
            or !method_exists($value, '__invoke')) {
            return $value;
        }
        return (isset($this->factories[$value]))
            ? $value($this)
            : $this->values[$name] = $value($this);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($name): bool
    {
        return $this->values->offsetExists($name);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($name)
    {
        $this->checkFrozen($name);
        if ($this->offsetExists($name)) {
            $this->values->offsetUnset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $keys = array_keys($this->values->getArrayCopy());
        $result = [];
        foreach ($keys as $key) {
            $value = $this[$key];
            $result[$key] = ($value instanceof ContainerInterface)
                ? $value->toArray()
                : $value;
        }
        return $result;
    }
}