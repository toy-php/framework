<?php

namespace Toy\Interfaces;

interface BaseObjectInterface extends \SplSubject
{

    /**
     * Получить состояние объекта
     * @return int
     */
    public function getState(): int;

    /**
     * Получить объект с соответствующим состоянием
     * @param int $state
     * @return $this|BaseObjectInterface
     */
    public function withState(int $state): BaseObjectInterface;

    /**
     * Преобразовать в массив
     * @param array $map
     * @return array
     */
    public function extractToArray(array $map): array;

    /**
     * Запрет изменения свойства
     * @param $name
     * @param $value
     */
    public function __set($name, $value);

    /**
     * Получить свойство
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * Проверить наличие свойства
     * @param $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * Запрет удаления свойства
     * @param $name
     */
    public function __unset($name);

    /**
     * Магия получения и изменения свойств
     * @param $method
     * @param $arguments
     * @return mixed|$this|BaseObjectInterface
     */
    public function __call($method, $arguments);
    
}