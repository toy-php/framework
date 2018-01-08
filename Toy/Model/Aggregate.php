<?php

declare(strict_types=1);

namespace Toy\Model;

use Toy\Interfaces\AggregateInterface;

class Aggregate extends Entity implements AggregateInterface
{

    public function __construct(int $id)
    {
        parent::__construct($id);
        $this->state = self::STATE_NEW;
    }

    public function __clone()
    {
        $this->state = self::STATE_DUTY;
    }

    /**
     * Является ли агрегат новым
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getState() === self::STATE_NEW;
    }

    /**
     * Является ли агрегат не измененным
     * @return bool
     */
    public function isClean(): bool
    {
        return $this->getState() === self::STATE_CLEAN;
    }

    /**
     * Является ли агрегат измененным
     * @return bool
     */
    public function isDuty(): bool
    {
        return $this->getState() === self::STATE_DUTY;
    }

    /**
     * Является ли агрегат удаленным
     * @return bool
     */
    public function isRemoved(): bool
    {
        return $this->getState() === self::STATE_REMOVED;
    }

    /**
     * Получить агрегат с состоянием чистых данных
     * @return $this|AggregateInterface
     */
    public function withCleanState(): AggregateInterface
    {
        return $this->withState(self::STATE_CLEAN);
    }

    /**
     * Получить агрегат с состоянием измененных данных
     * @return $this|AggregateInterface
     */
    public function withDutyState(): AggregateInterface
    {
        return $this->withState(self::STATE_DUTY);
    }

    /**
     * Получить агрегат с состоянием удаленных данных
     * @return $this|AggregateInterface
     */
    public function withRemovedState(): AggregateInterface
    {
        return $this->withState(self::STATE_REMOVED);
    }
}