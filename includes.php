<?php
@include_once 'custom_defines.php';

require_once 'defines.php';

require_once 'autoload.php';

foreach (glob("Controllers/*.php") as $filename)
{
    preg_match('/\/(\w+)\.php/', $filename, $matches);
    $class = 'App\\Controllers\\'.$matches[1];
    $class::initRoutes();
}