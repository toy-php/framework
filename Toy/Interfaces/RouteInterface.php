<?php

namespace Toy\Interfaces;

interface RouteInterface
{
    /**
     * Получить метод
     * @return string
     */
    public function getHttpMethod(): string;

    /**
     * Получить экземпляр маршрута с другим методом
     * @param string $httpMethod
     * @return RouteInterface
     */
    public function withHttpMethod(string $httpMethod): RouteInterface;

    /**
     * Получить обработчик маршрута
     * @return callable
     */
    public function getHandler(): callable;

    /**
     * Получить экземпляр маршрута с другим обработчиком маршрута
     * @param callable $handler
     * @return RouteInterface
     */
    public function withHandler(callable $handler): RouteInterface;

    /**
     * Получить совпадения запроса с шаблоном
     * @return array
     */
    public function getMatches(): array;

    /**
     * Проверка соответствия запроса шаблону маршрута
     * @param string $httpMethod
     * @param string $path
     * @return bool
     */
    public function isMatch(string $httpMethod, string $path): bool;

}