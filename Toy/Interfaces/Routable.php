<?php

namespace Toy\Interfaces;

interface Routable
{

    /**
     * Получить шаблон маршрута
     * @return string
     */
    public function getRegex(): string;

    /**
     * Получить экземпляр маршрута с другим шаблоном
     * @param string $regex
     * @return Routable
     */
    public function withRegex(string $regex): Routable ;

}