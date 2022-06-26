<?php

namespace Modules\User;

use Modules\Users\Contracts\UserRepositoryInterface;
use System\Contracts\IModule;
use System\Contracts\IRouter;

class Module implements IModule{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerRoutes(IRouter $router) : void {}

    public function getUser(){

    }

    public function getUsers(){

    }

    public function updateUser(){

    }

    public function deleteUser(){

    }
}