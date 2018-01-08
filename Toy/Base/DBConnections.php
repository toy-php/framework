<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Exceptions\Exception;

class DBConnections
{

    const DEFAULT_CONNECTION = 'default';

    /**
     * @var ExtPDO[]
     */
    static $connections = [];

    /**
     * Создать подключение к БД
     * @param string $name
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array $options
     * @return ExtPDO
     * @throws Exception
     */
    static public function create(
        string $name,
        string $dsn,
        string $username = null,
        string $password = null,
        array $options = [])
    {
        if (isset(static::$connections[$name])){
            throw new Exception(sprintf('Подключение к БД с именем "%s" уже существует'));
        }
        return static::$connections[$name] = new ExtPDO($dsn, $username, $password, $options);
    }

    /**
     * Создать подключение к БД по умолчанию
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array $options
     * @return ExtPDO
     * @throws Exception
     */
    static public function createDefault(
        string $dsn,
        string $username = null,
        string $password = null,
        array $options = []
    )
    {
        return static::create(static::DEFAULT_CONNECTION, $dsn, $username, $password, $options);
    }

    /**
     * Получить подключение к БД по имени
     * @param string $name
     * @return ExtPDO
     * @throws Exception
     */
    static public function get(string $name = self::DEFAULT_CONNECTION): ExtPDO
    {
        if (!isset(static::$connections[$name])){
            throw new Exception(sprintf('Подключение к БД с именем "%s" отсутствует'));
        }
        return static::$connections[$name];
    }
}