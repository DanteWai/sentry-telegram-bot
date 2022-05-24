<?php

namespace Modules\Bot;

use System\Contracts\IModule;
use System\Contracts\IRouter;

class Module implements IModule{
    public function registerRoutes(IRouter $router) : void {
        $router->addRoute("/^\/sentry-webhook$/", WebHookController::class, 'sentry');
    }
}