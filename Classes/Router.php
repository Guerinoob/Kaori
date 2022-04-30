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
     * The Router instance
     */
    private static $router;
    
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
     * Creates an instance of the router it it has not been created yet, then returns the instance
     * 
     * @return Router The router instance
     */
    public static function getInstance(): Router
    {
        if(static::$router)
            return static::$router;

        static::$router = new Router(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        return static::$router;
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
                if($route->matchUrl($this->url)) {
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
