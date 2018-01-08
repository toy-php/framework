<?php

namespace Toy\Router;

use Toy\Interfaces\ConverterInterface;

class Converter implements ConverterInterface
{

    protected $shortcuts = [
        ':d}' => ':[0-9]++}',             // digit only
        ':l}' => ':[a-z]++}',             // lower case
        ':u}' => ':[A-Z]++}',             // upper case
        ':a}' => ':[0-9a-zA-Z]++}',       // alphanumeric
        ':c}' => ':[0-9a-zA-Z+_\-\.]++}', // common chars
        ':nd}' => ':[^0-9/]++}',           // not digits
        ':xd}' => ':[^0-9/][^/]*+}',       // no leading digits
    ];
    const MATCH_GROUP_NAME = "\s*([a-zA-Z][a-zA-Z0-9_]*)\s*";
    const MATCH_GROUP_TYPE = ":\s*([^{}]*(?:\{(?-1)\}[^{}]*)*)";
    const MATCH_SEGMENT = "[^/]++";

    /**
     * Конвертация шаблони в регулярное выражение
     * @param string $pattern
     * @return string
     */
    public function convert(string $pattern): string
    {
        $ph = sprintf("\{%s(?:%s)?\}", self::MATCH_GROUP_NAME, self::MATCH_GROUP_TYPE);

        $result = preg_replace(
            [
                '~\[~x',
                '~\]~x',
                '~\{' . self::MATCH_GROUP_NAME . '\}~x',
                '~' . $ph . '~x',
            ],
            [
                '(?:',
                ')?',
                '{\\1:' . self::MATCH_SEGMENT . '}',
                '(?<${1}>${2})'
            ],
            strtr('/' . trim($pattern, '/'), $this->shortcuts)
        );
        return $result;
    }

}