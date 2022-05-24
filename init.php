<?php

spl_autoload_register(function($name){
    $path = str_replace('\\', '/', $name) . '.php';

    if(file_exists($path)){
        include_once($path);
    }
});

include_once('vendor/autoload.php');
include_once('System/functions.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();