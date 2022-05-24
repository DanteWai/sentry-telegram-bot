<?php

namespace System;

use System\Contracts\IController;

class BaseController implements IController
{
    protected array $env = [];

    public function setEnv(array $urlParams, array $get, array $post, array $server) : void{
        $this->env['params'] = $urlParams;
        $this->env['get'] = $get;
        $this->env['post'] = $post;
        $this->env['server'] = $server;
    }
}