<?php
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_NAME', 'kaori');

define('ROOT_URL', 'http://localhost');
define ('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].'');

define('CLASSES_DIR', DOCUMENT_ROOT.'/Classes');
define('THEME_PATH', DOCUMENT_ROOT.'/theme');
define('API_PATH', '/api');

define('ROOT_THEME_URL', ROOT_URL.THEME_PATH);
define('ROOT_API_URL', ROOT_URL.API_PATH);

define ('DOCUMENT_ROOT_API', DOCUMENT_ROOT.API_PATH);
define ('DOCUMENT_ROOT_THEME', DOCUMENT_ROOT.THEME_PATH);