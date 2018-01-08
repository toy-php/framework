<?php

namespace Toy\Interfaces;

interface ApplicationInterface extends ContainerInterface
{

    /**
     * Проверка и получение необходимых значений
     * @param array $params
     * @return array
     */
    public function required(array $params);

    /**
     * Расширить зарегистрированную функцию
     * @param $name
     * @return MiddlewareInterface
     */
    public function extend($name): MiddlewareInterface;

    /**
     * Получить сырые данные
     * @param string $name
     * @return mixed
     */
    public function getRaw(string $name);

    /**
     * Объявить функцию фабрикой
     * @param $callable
     * @return MiddlewareInterface
     */
    public function factory($callable): MiddlewareInterface;

}