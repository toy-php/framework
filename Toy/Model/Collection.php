<?php

declare(strict_types=1);

namespace Toy\Model;

use Toy\Exceptions\Exception;
use Toy\Interfaces\CollectionInterface;
use Toy\Interfaces\MetaDataInterface;
use Traversable;

class Collection implements CollectionInterface
{

    /**
     * Тип объектов коллекции
     * @var string
     */
    private $_type;

    /**
     * Массив объектов
     * @var \ArrayObject
     */
    private $_objects;

    private $_map;

    /**
     * @var MetaDataInterface
     */
    protected $metaData;

    public function __construct(string $objectClass)
    {
        $this->_type = $objectClass;
        $this->_objects = new \ArrayObject();
        $this->_map = new \SplObjectStorage();
    }

    /**
     * Получить мета-данные
     * @return MetaDataInterface
     */
    public function getMetaData(): MetaDataInterface
    {
        return $this->metaData ?: $this->metaData = new MetaData();
    }

    /**
     * Получить коллекцию с метаданными
     * @param MetaDataInterface $metaData
     * @return CollectionInterface
     */
    public function withMetaData(MetaDataInterface $metaData): CollectionInterface
    {
        if($this->metaData === $metaData){
            return $this;
        }
        $instance = clone $this;
        $instance->metaData = $metaData;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Проверка типа объекта
     * @param object $object
     * @throws Exception
     */
    protected function checkType($object)
    {
        if (!$object instanceof $this->_type) {
            throw new Exception('Неверный тип объекта');
        }
    }

    /**
     * @inheritdoc
     */
    public static function withObjects(string $objectClass, array $entities): CollectionInterface
    {
        $instance = new static($objectClass);
        foreach ($entities as $object) {
            $instance = $instance->withObject($object);
        }
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function withObject($object): CollectionInterface
    {
        $this->checkType($object);
        if ($this->_map->contains($object)) {
            return $this;
        }
        $instance = clone $this;
        $instance->_objects->append($object);
        $instance->_map->attach($object);
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function withoutObject($object): CollectionInterface
    {
        $this->checkType($object);

        if (!$this->_map->contains($object)) {
            return $this;
        }
        $key = array_search($object, $this->_objects->getArrayCopy());
        $instance = clone $this;
        $instance->_objects->offsetUnset($key);
        $instance->_map->detach($object);
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->_objects->offsetGet($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('Коллекцию нельзя менять');
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        return $this->_objects->offsetExists($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        throw new Exception('Коллекцию нельзя менять');
    }

    /**
     * Очистить коллекцию
     */
    public function clear()
    {
        $this->_objects->exchangeArray([]);
        $this->_map->removeAll($this->_map);
    }

    /**
     * Фильтрация коллекции.
     * Обходит каждый объект коллекции,
     * передавая его в callback-функцию.
     * Если callback-функция возвращает true,
     * данный объект из текущей коллекции попадает в результирующую коллекцию.
     * @param callable $function
     * @return CollectionInterface
     */
    public function filter(callable $function): CollectionInterface
    {
        $instance = clone $this;
        $instance->_objects->exchangeArray(
            array_filter($this->_objects->getArrayCopy(), $function)
        );
        $objects = $instance->_objects->getArrayCopy();
        foreach ($objects as $object) {
            $instance->_map->attach($object);
        }
        return $instance;
    }

    /**
     * Перебор всех объектов коллекции.
     * Возвращает новую коллекцию,
     * содержащую объекты после их обработки callback-функцией.
     * @param callable $function
     * @return CollectionInterface
     */
    public function map(callable $function): CollectionInterface
    {
        $instance = clone $this;
        $instance->_objects->exchangeArray(
            array_map($function, $this->_objects->getArrayCopy())
        );
        $objects = $instance->_objects->getArrayCopy();
        foreach ($objects as $object) {
            $instance->_map->attach($object);
        }
        return $instance;
    }

    /**
     * Итеративно уменьшает коллекцию к единственному значению
     * @param callable $function
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $function, $initial = null)
    {
        return array_reduce($this->_objects->getArrayCopy(), $function, $initial);
    }

    /**
     * Сортировка коллекции
     * @param callable $function
     * @return CollectionInterface
     */
    public function sort(callable $function): CollectionInterface
    {
        $this->_objects->uasort($function);
        return $this;
    }

    /**
     * Сортировать по полю
     * @param string $fieldName
     * @param string $direction
     * @return CollectionInterface
     * @throws Exception
     */
    public function sortByField(string $fieldName, string $direction = 'asc'): CollectionInterface
    {
        $direction = strtolower($direction);
        if ($direction != 'asc' and $direction != 'desc') {
            throw new Exception('Неизвестное направление сортировки');
        }
        $this->sort(function ($a, $b) use ($fieldName, $direction) {
            if (is_numeric($a->{$fieldName}) or is_numeric($b->{$fieldName})) {
                $compare = ($a->{$fieldName} <=> $b->{$fieldName});
            } else {
                $compare = strcmp(strtolower($a->{$fieldName}), strtolower($b->{$fieldName}));
            }
            return $direction == 'desc' ? -$compare : $compare;
        });
        return $this;
    }

    /**
     * Сортировка коллекции по ключам
     * @param string $direction
     * @return CollectionInterface
     * @throws Exception
     */
    public function keySort(string $direction = 'asc'): CollectionInterface
    {
        $direction = strtolower($direction);
        if ($direction != 'asc' and $direction != 'desc') {
            throw new Exception('Неизвестное направление сортировки');
        }
        $this->_objects->uksort(function ($a, $b) use ($direction) {
            $compare = ($a <=> $b);
            return $direction == 'desc' ? -$compare : $compare;
        });
        return $this;
    }

    /**
     * Поиск объекта по значению свойства
     * @param $property
     * @param $value
     * @return object|null
     */
    public function search($property, $value)
    {
        $offset = array_search($value, array_column($this->_objects->getArrayCopy(), $property));
        if ($offset !== false and $offset >= 0) {
            return $this->offsetGet($offset);
        }
        return null;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return $this->_objects->getIterator();
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return $this->_objects->count();
    }

    /**
     * Получить коллекцию в виде массива
     * Аргумент - функция преобразующая элементы коллекции в элементы массива
     * @param callable $function
     * @return array
     */
    public function toArray(callable $function = null): array
    {
        $function = $function ?: function($item){
            return $item;
        };
        $output = [];
        foreach ($this->_objects as $key => $object) {
            $output[$key] = $function($object, $key);
        }
        return $output;
    }
}