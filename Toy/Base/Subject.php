<?php

declare(strict_types=1);

namespace Toy\Base;

use SplObserver;

class Subject implements \SplSubject
{

    /**
     * Массив наблюдателей за субъектом
     * @var \SplObjectStorage
     */
    private $_observers;

    public function __construct()
    {
        $this->_observers = new \SplObjectStorage();
    }

    /**
     * Добавить наблюдателя за субъектом
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer)
    {
        if(!$this->_observers->contains($observer)){
            $this->_observers->attach($observer);
        }
    }

    /**
     * Исключить наблюдателя за субъектом
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer)
    {
        if($this->_observers->contains($observer)){
            $this->_observers->detach($observer);
        }
    }

    /**
     * Оповестить наблюдателей об изменении состояния
     */
    public function notify()
    {
        /** @var SplObserver $observer */
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }
}