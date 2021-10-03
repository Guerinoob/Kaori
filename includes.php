<?php
require_once 'defines.php';

require_once 'autoload.php';

foreach (glob(CLASSES_DIR."/*.php") as $filename)
{
    require_once $filename;
}

foreach (glob("Controllers/*.php") as $filename)
{
    preg_match('/\/(\w+)\.php/', $filename, $matches);
    $class = 'App\\Controllers\\'.$matches[1];
    $class::initRoutes();
}