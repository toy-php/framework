<?php

namespace Toy\Interfaces;

interface ViewModelInterface
{

    /**
     * Получить атрибут
     * @param $name
     * @return mixed
     */
    public function getAttribute(string $name);

    /**
     * Наличие атрибута
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool;

}