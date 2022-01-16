<?php

use App\Router;

ini_set('display_errors','on');

session_start();

require_once 'includes.php';

$router = Router::getInstance();
$router->run();
