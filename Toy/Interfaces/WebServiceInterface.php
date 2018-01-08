<?php

namespace Toy\Interfaces;

interface WebServiceInterface
{

    /**
     * Зарегистрировать сервис в веб приложении
     * @param WebApplicationInterface $application
     * @param array ...$arguments
     * @return void
     */
    static public function register(WebApplicationInterface $application, ...$arguments);
}