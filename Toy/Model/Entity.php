<?php

declare(strict_types=1);

namespace Toy\Model;

use Toy\Base\BaseObject;
use Toy\Exceptions\Exception;
use Toy\Interfaces\EntityInterface;

class Entity extends BaseObject implements EntityInterface
{

    protected $id;

    public function __construct(int $id)
    {
        parent::__construct();
        $this->id = $id;
    }

    /**
     * Получить идентификатор
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Запрет изменения идентификатора
     * @throws Exception
     */
    public function withId()
    {
        throw new Exception('Нельзя менять идентификатор сущности');
    }
}