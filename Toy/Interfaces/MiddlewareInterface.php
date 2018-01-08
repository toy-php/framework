<?php

namespace Toy\Interfaces;

interface MiddlewareInterface
{

    /**
     * Инициализация стека функций для стартовой функции
     * @param callable $callable
     */
    public function __construct(callable $callable);

    /**
     * Добавление функции в стек
     * @param callable $callable
     * @return MiddlewareInterface
     */
    public function then(callable $callable): MiddlewareInterface;

    /**
     * Выполнение стека функций
     * @param array ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments);

}