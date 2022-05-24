<?php

function dd($var){
    echo '<pre>' . var_export($var, true) . '</pre>';
    die();
}

function dump($var){
    echo '<pre>' . var_export($var, true) . '</pre>';
}