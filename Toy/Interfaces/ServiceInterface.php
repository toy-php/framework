<?php

namespace Toy\Interfaces;

interface ServiceInterface
{

    /**
     * Зарегистрировать сервис в приложении
     * @param ApplicationInterface $application
     * @param array ...$arguments
     * @return void
     */
    static public function register(ApplicationInterface $application, ...$arguments);

}