<?php

namespace Toy\Interfaces;

interface MetaDataInterface
{

    /**
     * Получить свойство мета данных
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * Установить свойство мета данных
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value);

}