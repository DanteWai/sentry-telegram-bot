<?php

function dd(): void
{
    $args = func_get_args();

    foreach ($args as $item) {
        echo '<pre>' . var_export($item, true) . '</pre>';
    }

    die();
}

function dump(): void
{
    $args = func_get_args();

    foreach ($args as $item) {
        echo '<pre>' . var_export($item, true) . '</pre>';
    }
}

function isJson($string): bool
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function appLog(string $string){
    $dir = "logs";
    if(!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents('logs/log'. date('d.m.Y'), $string . "\n", FILE_APPEND);
}