<?php

use System\Contracts\IRouter;
use System\Exceptions\Exc404;

class Router implements IRouter
{
    protected string $baseUrl;
    protected int $baseShift;
    protected array $routes = [];

    public function __construct(string $baseUrl = ''){
        $this->baseUrl = $baseUrl;
        $this->baseShift = strlen($this->baseUrl);
    }

    public function addRoute(string $url, string $controllerName, string $controllerMethod = 'index', array $params = []): void
    {
        $this->routes[] = [
            'path' => $url,
            'controller' => $controllerName,
            'method' => $controllerMethod,
            'params' => $params
        ];
    }

    public function resolvePath(string $url): array
    {
        $relativeUrl = substr($url, $this->baseShift);
        $route = $this->findPath($relativeUrl);
        $controller = new $route['c']();
        $controller->setEnviroment($route['params'], $_GET, $_POST, $_SERVER);

        return [
            'controller' => $controller,
            'method' => $route['m']
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