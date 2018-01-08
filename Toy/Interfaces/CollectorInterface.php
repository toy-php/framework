<?php

namespace Toy\Interfaces;

interface CollectorInterface
{

    /**
     * Добавить маршрут
     * @param string $httpMethod
     * @param string $pattern
     * @param callable $handler
     * @return CollectorInterface
     */
    public function addRoute(string $httpMethod, string $pattern, callable $handler): CollectorInterface;

    /**
     * Добавить группу маршрутов
     * @param string $groupPrefix
     * @param callable $groupFunction
     * @return CollectorInterface
     */
    public function addGroup(string $groupPrefix, callable $groupFunction): CollectorInterface;

    /**
     * Найти маршрут
     * @param string $httpMethod
     * @param string $path
     * @return RouteInterface|null
     */
    public function findRoute(string $httpMethod, string $path): ?RouteInterface;

}