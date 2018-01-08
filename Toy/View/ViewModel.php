<?php

namespace Toy\View;

use Toy\Exceptions\Exception;
use Toy\Interfaces\ViewModelInterface;

class ViewModel implements ViewModelInterface
{

    private $reflection;

    public function __construct()
    {
        $this->reflection = new \ReflectionClass($this);
    }

    /**
     * Получить атрибут
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getAttribute(string $name)
    {
        if ($this->hasAttribute($name)){
            $property = $this->reflection->getProperty($name);
            return $property->getValue($this);
        }
        throw new Exception(sprintf('Неизвестный атрибут "%s"', $name));
    }

    /**
     * Наличие атрибута
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        if ( $this->reflection->hasProperty($name)){
            $property = $this->reflection->getProperty($name);
            return $property->isPublic();
        }
        return false;
    }
}