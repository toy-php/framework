<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Exceptions\Exception;
use Toy\Interfaces\BaseObjectInterface;

class BaseObject extends Subject implements BaseObjectInterface
{

    private $reflection;

    protected $state = 0;

    public function __construct()
    {
        parent::__construct();
        $this->reflection = new \ReflectionClass($this);
    }

    /**
     * Создать объект с данными из массива
     * @param array $data
     * @param array $map
     * @param array $constructorAdditionalArguments
     * @return static
     */
    static public function createWithData(array $data, array $map, array $constructorAdditionalArguments = [])
    {
        $reflection = new \ReflectionClass(static::class);
        $mapParams = array_flip($map);
        $arguments = array_map(function (\ReflectionParameter $parameter) use ($data, $mapParams, $constructorAdditionalArguments) {
            $paramName = $parameter->getName();
            if (array_key_exists($paramName, $mapParams)) {
                $key = (isset($mapParams[$paramName]) and is_string($mapParams[$paramName]))
                    ? $mapParams[$paramName]
                    : $paramName;
                if (array_key_exists($key, $data)) {
                    $value = $data[$key];
                    $paramType = $parameter->getType()->getName();
                    return settype($value, $paramType) ? $value : $data[$key];
                }
            }
            if (array_key_exists($paramName, $constructorAdditionalArguments)) {
                return $constructorAdditionalArguments[$paramName];
            }
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new Exception(
                sprintf('В переданных данных не найдено значение параметра "%s" для инициализации класса "%s"',
                    $paramName,
                    static::class
                )
            );
        }, $reflection->getConstructor()->getParameters());

        /** @var static $object */
        $object = $reflection->newInstanceArgs($arguments);
        return $object;

    }

    /**
     * Преобразовать в массив
     * @param array $map
     * @return array
     */
    public function extractToArray(array $map): array
    {
        $output = [];
        foreach ($map as $propertyName => $key) {
            $propertyName = is_integer($propertyName) ? $key : $propertyName;
            if ($this->reflection->hasProperty($propertyName)) {
                $property = $this->reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $propertyValue = $property->getValue($this);
                if ($propertyValue instanceof BaseObjectInterface and is_array($key)) {
                    $output[$propertyName] = $propertyValue->extractToArray($key);
                } else {
                    $output[$key] = $propertyValue;
                }
            }
        }
        return $output;
    }

    /**
     * Получить состояние объекта
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * Получить объект с соответствующим состоянием
     * @param int $state
     * @return $this|BaseObjectInterface
     */
    public function withState(int $state): BaseObjectInterface
    {
        if ($this->state === $state) {
            return $this;
        }
        $instance = clone $this;
        $instance->state = $state;
        return $instance;
    }

    /**
     * Запрет изменения свойства
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception(sprintf('Свойство "%s" не доступно для изменений', $name));
    }

    /**
     * Получить свойство
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter) or is_callable([$this, $getter])) {
            return $this->$getter();
        }
        throw new Exception(sprintf('Свойство "%s" не доступно', $name));
    }

    /**
     * Проверить наличие свойства
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->hasProperty($name);
    }

    /**
     * Запрет удаления свойства
     * @param $name
     * @throws Exception
     */
    public function __unset($name)
    {
        throw new Exception(sprintf('Свойство "%s" не доступно для изменений', $name));
    }

    /**
     * Магия получения и изменения свойств
     * @param $method
     * @param $arguments
     * @return mixed|$this|BaseObject
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        if (preg_match('/^get([a-z0-9_]+)/i', $method, $matches)) {
            $name = lcfirst($matches[1]);
            return $this->getProperty($name);
        }
        if (preg_match('/^has([a-z0-9_]+)/i', $method, $matches)) {
            $name = lcfirst($matches[1]);
            return $this->hasProperty($name);
        }
        if (preg_match('/^with([a-z0-9_]+)/i', $method, $matches)) {
            $name = lcfirst($matches[1]);
            $value = array_shift($arguments);
            return $this->withProperty($name, $value);
        }
        throw new Exception(sprintf('Объект не имеет метода "%s" ', $method));
    }

    /**
     * Получение свойства
     * @param string $name
     * @return mixed|null
     * @throws Exception
     */
    protected function getProperty(string $name)
    {
        if ($this->hasProperty($name)) {
            $property = $this->reflection->getProperty($name);
            $property->setAccessible(true);
            return $property->getValue($this);
        }
        throw new Exception(sprintf('Объект не имеет свойства "%s" ', $name));
    }

    /**
     * Наличие свойства
     * @param string $name
     * @return bool
     */
    protected function hasProperty(string $name): bool
    {
        if ($this->reflection->hasProperty($name)) {
            $property = $this->reflection->getProperty($name);
            return $property->isProtected();
        }
        return false;
    }

    /**
     * Получить объект с измененным свойством
     * @param string $name
     * @param $value
     * @return $this|BaseObject
     * @throws Exception
     */
    protected function withProperty(string $name, $value): BaseObject
    {
        if ($this->hasProperty($name)) {
            $property = $this->reflection->getProperty($name);
            $property->setAccessible(true);
            $oldValue = $property->getValue($this);
            $this->checkType($property, $oldValue, $value);
            if ($oldValue === $value) {
                return $this;
            }
            $instance = clone $this;
            $instance->$name = $value;
            return $instance;
        }
        throw new Exception(sprintf('Объект не имеет свойства "%s" ', $name));
    }

    /**
     * Проверка типа переданных данных
     * @param \ReflectionProperty $property
     * @param $oldValue
     * @param $value
     * @throws Exception
     */
    protected function checkType(\ReflectionProperty $property, $oldValue, $value)
    {
        $docBlock = new DocBlock($property->getDocComment());
        if (!$docBlock->hasTag('var')) {
            return;
        }
        $type = $docBlock->tag('var')[0];
        switch ($type) {
            case 'string':
                $isInvalidType = !is_string($value);
                break;
            case 'int':
            case 'integer':
                $isInvalidType = !is_integer($value);
                break;
            case 'float':
                $isInvalidType = !is_float($value);
                break;
            case 'bool':
            case 'boolean':
                $isInvalidType = !is_bool($value);
                break;
            case 'array':
                $isInvalidType = !is_array($value);
                break;
            case 'static':
            case 'self':
            case '$this':
                $calledClass = get_called_class();
                $isInvalidType = (!$value instanceof $calledClass);
                break;
            case null:
                $isInvalidType = false;
                break;
            default:
                if (!is_object($value)) {
                    $isInvalidType = true;
                    break;
                }
                $namespace = $this->getCalledNamespace();
                $className = $namespace . '\\' . $type;
                $isInvalidType = ((!$value instanceof $className) and (!$value instanceof $type));
        }

        if ($isInvalidType) {
            $className = get_called_class();
            $methodName = 'with' . ucfirst($property->getName());
            throw new Exception(sprintf('Неверный тип данных. "%s::%s"', $className, $methodName));
        }

        if ($oldValue != null and gettype($oldValue) != gettype($value)) {
            throw new Exception('Неверный тип данных');
        }
    }

    /**
     * Получить namespace класса
     * @return string
     */
    protected function getCalledNamespace(): string
    {
        $path = explode('\\', get_called_class());
        array_pop($path);
        return implode('\\', $path);
    }

}