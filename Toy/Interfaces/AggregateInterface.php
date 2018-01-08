<?php

namespace Toy\Interfaces;

interface AggregateInterface extends EntityInterface
{

    const STATE_NEW = 0x00;

    const STATE_CLEAN = 0x01;

    const STATE_DUTY = 0x02;

    const STATE_REMOVED = 0x03;

    /**
     * Является ли агрегат новым
     * @return bool
     */
    public function isNew(): bool;

    /**
     * Является ли агрегат не измененным
     * @return bool
     */
    public function isClean(): bool;

    /**
     * Является ли агрегат измененным
     * @return bool
     */
    public function isDuty(): bool;

    /**
     * Является ли агрегат удаленным
     * @return bool
     */
    public function isRemoved(): bool;

    /**
     * Получить агрегат с состоянием чистых данных
     * @return $this|AggregateInterface
     */
    public function withCleanState(): AggregateInterface;

    /**
     * Получить агрегат с состоянием измененных данных
     * @return $this|AggregateInterface
     */
    public function withDutyState(): AggregateInterface;

    /**
     * Получить агрегат с состоянием удаленных данных
     * @return $this|AggregateInterface
     */
    public function withRemovedState(): AggregateInterface;

}