<?php

namespace Toy\Router;

use Toy\Exceptions\Exception;
use Toy\Interfaces\CollectorInterface;
use Toy\Interfaces\ConverterInterface;
use Toy\Interfaces\RouteInterface;

class Collector extends Group implements CollectorInterface
{

    static private $allowedMethods = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
        'head',
        'connect',
        'options',
        'trace',
    ];

    protected $converter;

    public function __construct(string $rootPrefix = '', ConverterInterface $converter = null)
    {
        $this->converter = $converter ?: new Converter();
        $rootPrefixRegex = $this->converter->convert($rootPrefix);
        parent::__construct($rootPrefixRegex);
    }

    /**
     * Добавить маршрут
     * @param string $httpMethod
     * @param string $pattern
     * @param callable $handler
     * @return CollectorInterface
     * @throws Exception
     */
    public function addRoute(string $httpMethod, string $pattern, callable $handler): CollectorInterface
    {
        if (!in_array($httpMethod, static::$allowedMethods)){
            throw new Exception('Метод не определен');
        }
        $regex = $this->converter->convert($pattern);
        $this->attach(new Route($httpMethod, $regex, $handler));
        return $this;
    }

    /**
     * Добавить группу маршрутов
     * @param string $groupPrefix
     * @param callable $groupFunction
     * @return CollectorInterface
     */
    public function addGroup(string $groupPrefix, callable $groupFunction): CollectorInterface
    {
        $groupPrefixRegex = $this->converter->convert($groupPrefix);
        $group = new Group($groupPrefixRegex);
        $groupFunction($group);
        $this->attach($group);
        return $this;
    }

    /**
     * Найти маршрут
     * @param string $httpMethod
     * @param string $path
     * @return RouteInterface|null
     */
    public function findRoute(string $httpMethod, string $path): ?RouteInterface
    {
        /** @var Route $route */
        foreach ($this as $route) {
            if ($route->isMatch($httpMethod, $path)) {
                return $route;
            }
        }
        return null;
    }

}