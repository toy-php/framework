<?php

declare(strict_types=1);

namespace Toy\Model;

use Toy\Base\ExtPDO;
use Toy\Exceptions\Exception;
use Toy\Interfaces\AggregateInterface;
use Toy\Interfaces\CollectionInterface;
use Toy\Interfaces\MapperInterface;
use SplSubject;

abstract class DBMapper implements MapperInterface
{

    /**
     * @var ExtPDO
     */
    protected $pdo;

    /**
     * @var string
     */
    private $aggregateType;

    /**
     * @var AggregateInterface|null
     */
    private $aggregate;

    /**
     * @var Collection
     */
    private $collection;

    public function __construct(ExtPDO $pdo, string $aggregateType)
    {
        $this->pdo = $pdo;
        $this->aggregateType = $aggregateType;
        $this->collection = new Collection($this->aggregateType);
    }

    /**
     * Проверка типа объекта
     * @param AggregateInterface $aggregate
     * @throws Exception
     */
    protected function checkType(AggregateInterface $aggregate)
    {
        $aggregateType = $this->aggregateType;
        if (!$aggregate instanceof $aggregateType) {
            throw new Exception('Неверный тип объекта');
        }
    }

    /**
     * Добавить агрегат в коллекцию
     * @param AggregateInterface $aggregate
     */
    protected function addAggregateToCollection(AggregateInterface $aggregate)
    {
        $this->collection = $this->collection->withObject($aggregate);
    }

    /**
     * Добавить метаданные в коллекцию
     * @param array $metaData
     */
    protected function addMetaDataToCollection(array $metaData)
    {
        $meta = new MetaData();
        foreach ($metaData as $key => $value) {
            $meta->__set($key, $value);
        }
        $this->collection = $this->collection->withMetaData($meta);
    }

    /**
     * Обновить состояние источника данных
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject)
    {
        /** @var AggregateInterface $subject */
        if ($subject->isDuty()){
            $this->save($subject);
        }
        if ($subject->isRemoved()){
            $this->remove($subject);
        }
    }

    /**
     * Сохранить агрегат
     * @param AggregateInterface $aggregate
     * @return void
     */
    abstract protected function save(AggregateInterface $aggregate);

    /**
     * Удалить агрегат
     * @param AggregateInterface $aggregate
     * @return void
     */
    abstract protected function remove(AggregateInterface $aggregate);

    /**
     * Установить агрегат
     * @param AggregateInterface $aggregate
     * @return $this
     * @throws Exception
     */
    protected function setAggregate(AggregateInterface $aggregate): MapperInterface
    {
        $this->checkType($aggregate);
        $aggregate->attach($this);
        $this->aggregate = $aggregate->withCleanState();
        return $this;
    }

    /**
     * Получить агрегат
     * @return AggregateInterface|null
     */
    public function getAggregate(): ?AggregateInterface
    {
        return $this->aggregate;
    }

    /**
     * Получить коллекцию агрегатов
     * @return CollectionInterface|AggregateInterface[]
     */
    public function getCollection(): CollectionInterface
    {
        return $this->collection;
    }
}