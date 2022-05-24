<?php

namespace System\Contracts;

interface IController{
    public function setEnv(array $urlParams, array $get, array $post, array $server) : void;
}