<?php
include_once('init.php');

use Modules\Bot\Module as BotModule;
use System\Exceptions\Exc404;
use System\ModulesDispatcher;
use System\Router;


try {
    $modules = new ModulesDispatcher();
    $modules->add(new BotModule());
    $router = new Router();

    $modules->registerRoutes($router);

    $uri = $_SERVER['REQUEST_URI'];
    $activeRoute = $router->resolvePath($uri);
    $activeRoute['controller']->{$activeRoute['method']}();
}
catch(Exc404 $e){
    echo '404';
}
catch(Throwable $e){
    echo 'nice show error - ' . $e->getMessage();
}