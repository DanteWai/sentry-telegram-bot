<?php

function dd(){
    $args = func_get_args();

    foreach ($args as $item){
        echo '<pre>' . var_export($item, true) . '</pre>';
    }

    die();
}

function dump(){
    $args = func_get_args();

    foreach ($args as $item){
        echo '<pre>' . var_export($item, true) . '</pre>';
    }
}