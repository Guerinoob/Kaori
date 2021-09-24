<?php
/**
 * Router class
 */

namespace App;

/**
 * This class stores every route and can run the correct one
 */
class Router {    
    /**
     * The base URL of the site
     *
     * @var string
     */
    private $url;
        
    /**
     * Array of every route that can be used
     *
     * @var array
     */
    private $routes = [];
    
    /**
     * Constructor - Setups internal variable
     *
     * @param  string $url
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }
    
    /**
     * Adds a route to the site
     *
     * @param  string $path The path to the route
     * @param  callable $callback The function that will be executed on that route
     * @param  array $methods The HTTP methods that will trigger the route
     * @return void
     */
    public function addRoute($path, $callback, $methods): void 
    {
        foreach($methods as $method) {
            $route = new Route($path, $callback);
            $this->routes[$method][] = $route;
        }
    }
    
    /**
     * Executes the route corresponding to the client request
     *
     * @return void
     */
    public function run(): void
    {
        $found = false;

        if(isset($this->routes[$_SERVER['REQUEST_METHOD']]) && is_array($this->routes[$_SERVER['REQUEST_METHOD']]) && count($this->routes[$_SERVER['REQUEST_METHOD']]) > 0) {
            foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
                if($route->match($this->url)) {
                    $route->run();
                    $found = true;
                    break;
                }
            }
        }
        
        if(!$found) {
            // TODO : 404 redirection
            echo 'No route';
        }
    }
}

//We instantiate the router and make it global in order to be able to access it anywhere with only one instance
global $router;
$router = new Router(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));