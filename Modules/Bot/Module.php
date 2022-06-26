<?php

namespace Modules\Bot;

use System\Contracts\IModule;
use System\Contracts\IRouter;

class Module implements IModule{
    public function registerRoutes(IRouter $router) : void {
        $router->addRoute("/^\/api\/sentry\/webhook$/", WebHookController::class, 'sentry');
        $router->addRoute("/^\/api\/sentry\/alert-rule-action$/", WebHookController::class, 'sentry');

        $router->addRoute("/^\/api\/telegram\/webhook$/", WebHookController::class, 'telegram');
    }
}