<?php

class Route {
    private $path;
    private $callback;

    private $params = [];

    public function __construct($path, $callback)
    {
        $this->path = trim($path, '/');
        $this->callback = $callback;
    }

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

    public function run() {
        call_user_func_array($this->callback, $this->params);
    }
}