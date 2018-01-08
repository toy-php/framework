<?php

namespace Toy\Interfaces;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable
{

    /**
     * Получить мета-данные
     * @return MetaDataInterface
     */
    public function getMetaData(): MetaDataInterface;

    /**
     * Получить коллекцию с метаданными
     * @param MetaDataInterface $metaData
     * @return CollectionInterface
     */
    public function withMetaData(MetaDataInterface $metaData): CollectionInterface;

    /**
     * Получить тип сущности которую содержит коллекция
     * @return string
     */
    public function getType(): string;

    /**
     * Получить экземпляр коллекции с массивом объектов
     * @param string $objectClass
     * @param object[] $objects
     * @return CollectionInterface
     */
    public static function withObjects(string $objectClass, array $objects) : CollectionInterface;

    /**
     * Получить экземпляр коллекции с новым объектом
     * @param object $object
     * @return CollectionInterface
     */
    public function withObject($object) : CollectionInterface;

    /**
     * Получить экземпляр коллекции без указанного объекта
     * @param object $object
     * @return CollectionInterface
     */
    public function withoutObject($object) : CollectionInterface;

    /**
     * Получить объект
     * @param mixed $offset
     * @return object
     */
    public function offsetGet($offset);

    /**
     * Коллекции должны быть иммутабельны,
     * метод необходимо заглушить
     * @param mixed $offset
     * @param object $value
     * @throws \Throwable
     */
    public function offsetSet($offset, $value);

    /**
     * Наличие объекта
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset) : bool;

    /**
     * Коллекции должны быть иммутабельны,
     * метод необходимо заглушить
     * @param mixed $offset
     * @return void
     * @throws \Throwable
     */
    public function offsetUnset($offset);

    /**
     * Очистить коллекцию
     */
    public function clear();

    /**
     * Фильтрация коллекции.
     * Обходит каждый объект коллекции,
     * передавая его в callback-функцию.
     * Если callback-функция возвращает true,
     * данный объект из текущей коллекции попадает в результирующую коллекцию.
     * @param callable $function
     * @return CollectionInterface
     */
    public function filter(callable $function): CollectionInterface;

    /**
     * Перебор всех объектов коллекции.
     * Возвращает новую коллекцию,
     * содержащую объекты после их обработки callback-функцией.
     * @param callable $function
     * @return CollectionInterface
     */
    public function map(callable $function): CollectionInterface;

    /**
     * Итеративно уменьшает коллекцию к единственному значению
     * @param callable $function
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $function, $initial = null);

    /**
     * Сортировка коллекции
     * @param callable $function
     * @return CollectionInterface
     */
    public function sort(callable $function): CollectionInterface;

    /**
     * Сортировать по полю
     * @param string $fieldName
     * @param string $direction
     * @return CollectionInterface
     */
    public function sortByField(string $fieldName, string $direction = 'asc'): CollectionInterface;

    /**
     * Сортировка коллекции по ключам
     * @param string $direction
     * @return CollectionInterface
     */
    public function keySort(string $direction = 'asc'): CollectionInterface;

    /**
     * Поиск объекта по значению свойства
     * @param $property
     * @param $value
     * @return object|null
     */
    public function search($property, $value);

    /**
     * Получить коллекцию в виде массива
     * Аргумент - функция преобразующая элементы коллекции в элементы массива
     * @param callable $function
     * @return array
     */
    public function toArray(callable $function = null): array;

}