<?php

namespace Toy\Interfaces;

interface ConverterInterface
{

    /**
     * Конвертация шаблони в регулярное выражение
     * @param string $pattern
     * @return string
     */
    public function convert(string $pattern): string;
}