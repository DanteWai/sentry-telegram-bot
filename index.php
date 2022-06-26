<?php
include_once('init.php');

use Modules\Bot\Module as BotModule;
use System\Exceptions\Exc404;
use System\ModulesDispatcher;
use System\Router;


try {
    $builder = new DI\ContainerBuilder();
    $builder->addDefinitions('Config/di.php');
    $container = $builder->build();

    $modules = new ModulesDispatcher();
    $modules->add(new BotModule());
    $router = new Router('', $container);

    $modules->registerRoutes($router);
    $uri = $_SERVER['REQUEST_URI'];
    $activeRoute = $router->resolvePath($uri);
    $activeRoute['controller']->{$activeRoute['method']}();
}
catch(Exc404 $e){
    appLog($e->getMessage());
    echo '404';
}
catch(Throwable $e){
    appLog($e->getMessage());
    echo 'nice show error - ' . $e->getMessage();
}