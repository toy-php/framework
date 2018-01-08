<?php

$basedir = dirname(__DIR__);

$search = [
    dirname($basedir) . '/autoload.php',
    $basedir.'/vendor/autoload.php',
];

foreach ($search as $auto) {
    if (file_exists($auto)) {
        $loader = require $auto;
        break;
    }
}