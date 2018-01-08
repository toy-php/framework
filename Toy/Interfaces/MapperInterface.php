<?php

namespace Toy\Interfaces;

use SplSubject;

interface MapperInterface extends \SplObserver
{

    /**
     * Найти по идентификатору
     * @param int $id
     * @return MapperInterface
     */
    public function byId(int $id): MapperInterface;

    /**
     * Получить агрегат
     * @return AggregateInterface|null
     */
    public function getAggregate(): ?AggregateInterface;

    /**
     * Получить коллекцию агрегатов
     * @return CollectionInterface|AggregateInterface[]
     */
    public function getCollection(): CollectionInterface;

    /**
     * Обновить состояние источника данных
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject);

}