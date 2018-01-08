<?php

namespace Toy\Router;

use Toy\Interfaces\GroupInterface;
use Toy\Interfaces\Routable;
use Toy\Interfaces\RouteInterface;

class Group extends AbstractRoute implements GroupInterface, \IteratorAggregate
{

    protected $routs;

    public function __construct(string $groupPrefixRegex)
    {
        parent::__construct($groupPrefixRegex);
        $this->routs = new \SplObjectStorage();
    }

    /**
     * Вложить маршрут или группу маршрутов
     * @param Routable $route
     */
    public function attach(Routable $route)
    {
        if ($route instanceof Group){
            foreach ($route as $item) {
                $this->attach($item);
            }
            return;
        }
        $regex = $this->regex . $route->getRegex();
        $route = $route->withRegex($regex);
        $this->routs->attach($route);
    }

    /**
     * Исключить маршут из группы
     * @param RouteInterface $route
     */
    public function detach(RouteInterface $route)
    {
        $this->routs->detach($route);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \IteratorIterator($this->routs);
    }

}