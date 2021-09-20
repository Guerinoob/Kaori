<?php

session_start();

require_once 'includes.php';

global $router;
$router->run();
