<?php

declare(strict_types=1);

namespace Toy\Model;

use Toy\Interfaces\MetaDataInterface;

class MetaData implements MetaDataInterface
{

    protected $data = [];

    /**
     * Получить свойство мета данных
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Установить свойство мета данных
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}