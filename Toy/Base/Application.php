<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Exceptions\Exception;
use Toy\Interfaces\ApplicationInterface;
use Toy\Interfaces\MiddlewareInterface;

class Application extends Container implements ApplicationInterface
{

    /**
     * @var \SplObjectStorage
     */
    protected $factories;

    public function __construct(array $defaults = [], bool $frozenValues = true)
    {
        parent::__construct($defaults, $frozenValues);
        $this->factories = new \SplObjectStorage();
    }

    /**
     * Добавить значение
     * @param mixed $name
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($name, $value)
    {
        if (is_callable($value) and !$value instanceof MiddlewareInterface) {
            $value = $this->createMiddleware($value);
        }
        parent::offsetSet($name, $value);
    }

    /**
     * Получить значение
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function offsetGet($name)
    {
        if ($this->offsetExists($name)) {
            $value = $this->getRaw($name);
            if ($this->factories->contains($value)){
                return $value($this);
            }
        }
        return parent::offsetGet($name);
    }

    /**
     * Исключить значение
     * @param string $name
     */
    public function offsetUnset($name)
    {
        if ($this->offsetExists($name)) {
            $value = $this->values[$name];
            if (!is_object($value)
                or !method_exists($value, '__invoke')) {
                $this->factories->detach($value);
            }
        }
        parent::offsetUnset($name);
    }

    /**
     * Фабричный метод создания Middleware объекта
     * @param callable $function
     * @return Middleware
     */
    protected function createMiddleware(callable $function)
    {
        return new Middleware($function);
    }

    /**
     * Проверка и получение необходимых компонент
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function required(array $params)
    {
        $result = [];
        foreach ($params as $name => $param) {
            switch (gettype($name)) {
                case 'string':
                    if (!$this->offsetExists($name)) {
                        throw new Exception(
                            sprintf('Необходимый компонент %s ' .
                                'не зарегистрирован в ядре', $name)
                        );
                    }
                    $value = $this->offsetGet($name);
                    if (!$value instanceof $param) {
                        throw new Exception(
                            sprintf('Компонент %s не реализует ' .
                                'необходимый интерфейс "%s"', $name, $param)
                        );
                    }
                    $result[$name] = $value;
                    break;
                case 'integer':
                    if (!$this->offsetExists($param)) {
                        throw new Exception(
                            sprintf('Необходимый компонент %s ' .
                                'не зарегистрирован в ядре', $param)
                        );
                    }
                    $result[$name] = $this->offsetGet($param);
                    break;
                default:
                    throw new Exception('Неверный тип ключа');
            }
        }
        return $result;
    }

    /**
     * Расширить зарегистрированную функцию
     * @param $name
     * @return MiddlewareInterface
     * @throws Exception
     */
    public function extend($name): MiddlewareInterface
    {
        if ($this->offsetExists($name)) {
            $value = $this->getRaw($name);
            if ($value instanceof MiddlewareInterface) {
                return $value;
            }
            throw new Exception('Значение не является функцией');
        }
        throw new Exception(sprintf('Значение по ключу "%s" ' .
            'не найдено', $name));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getRaw(string $name)
    {
        if (!$this->offsetExists($name)) {
            throw new Exception(sprintf('Ключ "%s" не найден', $name));
        }
        return $this->values[$name];
    }

    /**
     * @inheritdoc
     */
    public function factory($callable): MiddlewareInterface
    {
        if (!is_object($callable)
            or !method_exists($callable, '__invoke')) {
            throw new Exception('Неверная функция');
        }
        $middleware = $this->createMiddleware($callable);
        $this->factories->attach($middleware);
        return $middleware;
    }
}