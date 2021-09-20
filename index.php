<?php
ini_set('display_errors','on');

session_start();

require_once 'includes.php';

global $router;
$router->run();
