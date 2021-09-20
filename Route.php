<?php
/**
 * Route class
 */

/**
 * This class represents one route and binds the route to its callback method
 */
class Route {    
    /**
     * The path to the route
     *
     * @var string
     */
    private $path;

        
    /**
     * The callback function used on the route
     *
     * @var callable
     */
    private $callback;
    
    /**
     * The parameters in the path (not query parameters), used as arguments for the callback
     *
     * @var array
     */
    private $params = [];
    
    /**
     * Constructor - Setups internal variable
     *
     * @param  string $path
     * @param  callable $callback
     * @return void
     */
    public function __construct($path, $callback)
    {
        $this->path = trim($path, '/');
        $this->callback = $callback;
    }
    
    /**
     * Checks if the given URL corresponds to this route
     *
     * @param  string $url The URL to test
     * @return bool Returns true if it matches, false is it doesn't
     */
    public function match($url) {
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);

        $regex = "#^$path$#i";

        if(!preg_match($regex, $url, $matches))
            return false;

        array_shift($matches);
        $this->params = $matches;

        return true;
    }
    
    /**
     * Executes the callback function
     *
     * @return void
     */
    public function run() {
        call_user_func_array($this->callback, $this->params);
    }
}