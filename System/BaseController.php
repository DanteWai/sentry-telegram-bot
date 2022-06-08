<?php

namespace System;

use System\Contracts\IController;

class BaseController implements IController
{
    protected array $env = [];

    public function setEnv(array $urlParams, array $get, array $post, array $headers) : void{
        $this->env['params'] = $urlParams;
        $this->env['get'] = $get;
        $this->env['post'] = $post;
        $this->env['headers'] = $headers;
    }
}