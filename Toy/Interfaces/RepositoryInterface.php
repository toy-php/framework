<?php

namespace Toy\Interfaces;

interface RepositoryInterface extends ServiceInterface
{

    /**
     * Найти агрегат
     * @return MapperInterface
     */
    public function find(): MapperInterface;

    /**
     * Сохранить агрегат
     * @param AggregateInterface $aggregate
     * @return void
     */
    public function save(AggregateInterface $aggregate);

    /**
     * Удалить агрегат
     * @param AggregateInterface $aggregate
     * @return void
     */
    public function remove(AggregateInterface $aggregate);

}