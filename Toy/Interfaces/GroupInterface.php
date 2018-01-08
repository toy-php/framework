<?php

namespace Toy\Interfaces;

interface GroupInterface
{

    /**
     * Вложить маршрут или группу маршрутов
     * @param Routable $route
     */
    public function attach(Routable $route);

    /**
     * Исключить маршут из группы
     * @param RouteInterface $route
     */
    public function detach(RouteInterface $route);

}