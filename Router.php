<?php

class Router {
    private $url;
    private $routes = [];

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function addRoute($path, $callback, $methods) {
        foreach($methods as $method) {
            $route = new Route(SUBDIR.$path, $callback);
            $this->routes[$method][] = $route;
        }
    }

    public function run() {
        if(isset($this->routes[$_SERVER['REQUEST_METHOD']]) && is_array($this->routes[$_SERVER['REQUEST_METHOD']]) && count($this->routes[$_SERVER['REQUEST_METHOD']]) > 0) {
            foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
                if($route->match($this->url)) {
                    return $route->run();
                }
            }
        }

        // TODO : 404 redirection
        echo 'No route';
    }
}

global $router;
$router = new Router($_SERVER['REQUEST_URI']);