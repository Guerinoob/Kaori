<?php
require_once 'defines.php';

require_once 'autoload.php';

require_once 'Database.php';
require_once 'Router.php';
require_once 'Route.php';
require_once 'BaseController.php';

foreach (glob("Controllers/*.php") as $filename)
{
    preg_match('/\/(\w+)\.php/', $filename, $matches);
    $class = 'App\\Controllers\\'.$matches[1];
    new $class();
}