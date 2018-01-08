<?php

namespace Toy\Router;

use Toy\Interfaces\Routable;

abstract class AbstractRoute implements Routable
{

    /**
     * Шаблон запроса
     * @var string
     */
    protected $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * Получить шаблон маршрута
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * Получить экземпляр маршрута с другим шаблоном
     * @param string $regex
     * @return Routable
     */
    public function withRegex(string $regex): Routable
    {
        if ($this->regex === $regex) {
            return $this;
        }
        $instance = clone $this;
        $instance->regex = $regex;
        return $instance;
    }

}