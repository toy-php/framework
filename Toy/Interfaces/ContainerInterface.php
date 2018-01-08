<?php

namespace Toy\Interfaces;

interface ContainerInterface extends \ArrayAccess
{
    /**
     * Проверка наличия значения в контейнере по ключу
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool;

    /**
     * Получить значение контейнера по ключу
     * если значением является исполняемая фунция,
     * то возвращается результат её выполнения
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset);

    /**
     * Добавить значение в контейнер
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value);

    /**
     * Исключить значение из контейнера по ключу
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset);

    /**
     * Получить содержимое контейнера в виде массива
     * @return array
     */
    public function toArray():array;

}