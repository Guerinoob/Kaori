<?php
require_once 'defines.php';

require_once 'autoload.php';

require_once 'DB.php';
require_once 'Router.php';
require_once 'Route.php';

foreach (glob("Controllers/*.php") as $filename)
{
    require_once $filename;
}