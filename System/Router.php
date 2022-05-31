<?php

namespace System;

use System\Contracts\IRouter;
use System\Exceptions\Exc404;
use DI\Container;

class Router implements IRouter
{
    protected string $baseUrl;
    protected int $baseShift;
    protected array $routes = [];
    protected Container $container;

    public function __construct(string $baseUrl, Container $container){
        $this->baseUrl = $baseUrl;
        $this->baseShift = strlen($this->baseUrl);
        $this->container = $container;
    }

    public function addRoute(string $url, string $controllerName, string $controllerMethod = 'index', array $paramsMap = []): void
    {
        $this->routes[] = [
            'path' => $url,
            'controller' => $controllerName,
            'method' => $controllerMethod,
            'paramsMap' => $paramsMap
        ];
    }

    public function resolvePath(string $url): array
    {
        $relativeUrl = substr($url, $this->baseShift);

        $route = $this->findPath($relativeUrl);

        $controller = $this->container->get($route['controller']);
        $controller->setEnv($route['params'], $_GET, $_POST, $_SERVER);

        return [
            'controller' => $controller,
            'method' => $route['method']
        ];
    }

    protected function findPath(string $url) : array{
        $activeRoute = null;

        foreach($this->routes as $route){
            $matches = [];

            if(preg_match($route['path'], $url, $matches)){
                $route['params'] = [];

                foreach($route['paramsMap'] as $i => $key){
                    if(isset($matches[$i])){
                        $route['params'][$key] = $matches[$i];
                    }
                }

                $activeRoute = $route;
            }
        }

        if($activeRoute === null){
            throw new Exc404('Page not found');
        }

        return $activeRoute;
    }
}