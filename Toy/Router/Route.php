<?php

namespace Toy\Router;

use Toy\Interfaces\RouteInterface;

class Route extends AbstractRoute implements RouteInterface
{

    /**
     * HTTP метод маршрута
     * @var string
     */
    protected $httpMethod;

    /**
     * Обработчик маршрута
     * @var callable
     */
    protected $handler;

    /**
     * Совпадения запроса с шаблоном
     * @var array
     */
    protected $matches = [];

    public function __construct(string $httpMethod, string $regex, callable $handler)
    {
        parent::__construct(rtrim($regex, '/'));
        $this->httpMethod = $httpMethod;
        $this->handler = $handler;
    }

    /**
     * Получить метод
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * Получить экземпляр маршрута с другим методом
     * @param string $httpMethod
     * @return RouteInterface
     */
    public function withHttpMethod(string $httpMethod): RouteInterface
    {
        if ($this->httpMethod === $httpMethod) {
            return $this;
        }
        $instance = clone $this;
        $instance->httpMethod = $httpMethod;
        return $instance;
    }

    /**
     * Получить обработчик маршрута
     * @return callable
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }

    /**
     * Получить экземпляр маршрута с другим обработчиком маршрута
     * @param callable $handler
     * @return RouteInterface
     */
    public function withHandler(callable $handler): RouteInterface
    {
        if ($this->handler === $handler) {
            return $this;
        }
        $instance = clone $this;
        $instance->handler = $handler;
        return $instance;
    }

    /**
     * Получить совпадения запроса с шаблоном
     * @return array
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     * Проверка соответствия запроса шаблону маршрута
     * @param string $httpMethod
     * @param string $path
     * @return bool
     */
    public function isMatch(string $httpMethod, string $path): bool
    {
        $regex = '#^' . $this->getRegex() . '$#is';
        if (strnatcasecmp($httpMethod, $this->httpMethod) === 0 and (bool)preg_match($regex, $path, $matches)) {
            $this->matches = $matches;
            return true;
        }
        return false;
    }

}