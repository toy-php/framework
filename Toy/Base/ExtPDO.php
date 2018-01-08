<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Exceptions\Exception;

class ExtPDO extends \PDO
{

    const ATTR_PROFILING = 'profiling';

    protected $logOn = false;

    public function __construct($dsn, $username, $password, $options)
    {
        parent::__construct($dsn, $username, $password, $options);
        $this->logOn = isset($options[static::ATTR_PROFILING])
            ? $options[static::ATTR_PROFILING] === true
            : false;
        if ($this->logOn) {
            $this->query('set profiling=1');
        }
    }

    /**
     * Получить лог запросов
     * @return array
     */
    public function getLog(): array
    {
        if ($this->logOn) {
            $stmt = $this->query('show profiles');
            $this->query('set profiling=0');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Выполнить транзакцию
     * @inheritdoc
     */
    public function transaction(callable $transaction)
    {
        $this->beginTransaction();
        try {
            $result = $transaction($this);
            if ($result === false) {
                $this->rollBack();
            } else {
                $this->commit();
            }
            return $result;
        } catch (\Throwable $exception) {
            $this->rollBack();
            throw $exception;
        }
    }

    /**
     * Выполнить SQL запрос
     * Типизация данных в запросе
     * ?s - строка
     * ?i - целое число
     * ?f - число с плавающей точкой
     * ?u - для выполнения запроса UPDATE Пример UPDATE ?t SET ?u WHERE id =?i
     * ?a - для выполнения запроса IN Пример SELECT * FROM ?t WHERE id IN (?a)
     * ?v - для выполнения запроса INSERT Пример INSERT INTO ?t ?v
     * ?c - для выбора необходимых колонок таблицы. Пример: SELECT ?c FROM ?t
     * ?t - имя таблицы. Пример: SELECT * FROM ?t
     * @param string $sql
     * @param array $bindings
     * @return \PDOStatement
     * @throws Exception
     */
    public function executeSql(string $sql, array $bindings = []): \PDOStatement
    {
        $sqlArray = preg_split('~(\?[sifuakvct])~u', $sql, 0, PREG_SPLIT_DELIM_CAPTURE);
        $paramsNum = count($bindings);
        $sqlArrayNum = floor(count($sqlArray) / 2);
        if ($paramsNum != $sqlArrayNum) {
            throw new Exception('Несоответствие переданных параметров с количеством параметров в запросе');
        }
        $query = '';
        $bindParams = [];
        foreach ($sqlArray as $i => $chunk) {
            if ( ($i % 2) == 0 ) {
                $query .= $chunk;
                continue;
            }
            $param = array_shift($bindings);
            switch ($chunk)
            {
                case '?s':
                    $query .= '?';
                    $bindParams[] = filter_var($param, FILTER_SANITIZE_STRING);
                    break;
                case '?i':
                    $query .= '?';
                    $bindParams[] = filter_var($param, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case '?f':
                    $query .= '?';
                    $bindParams[] = filter_var($param, FILTER_SANITIZE_NUMBER_FLOAT);
                    break;
                case '?u':
                    $set = '';
                    foreach ($param as $key => $value) {
                        $set .= $key . ' = ?, ';
                        $bindParams[] = $value;
                    }
                    $query .= rtrim($set, ', ');
                    break;
                case '?a':
                    $query .= '(' . implode(', ', array_fill(0, count($param), '?')) . ')';
                    $bindParams = array_merge($bindParams, $param);
                    break;
                case '?v':
                    $query .= '(' . implode(', ', array_keys($param)) . ')';
                    $query .= ' VALUES ';
                    $query .= '(' . implode(', ', array_fill(0, count($param), '?')) . ')';
                    $bindParams = array_merge($bindParams, array_values($param));
                    break;
                case '?c':
                    $query .= implode(', ', $param);
                    break;
                case '?t':
                    $query .= $param;
                    break;
            }
        }
        $stmt = $this->prepare($query);
        $stmt->execute($bindParams);
        return $stmt;
    }

}