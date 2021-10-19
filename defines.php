<?php
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_NAME', 'kaori');

define('SITENAME', 'Kaori');

define('ROOT_URL', 'http://localhost');
define ('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].'');

define('CLASSES_DIR', DOCUMENT_ROOT.'/Classes');
define('THEME_PATH', DOCUMENT_ROOT.'/Theme');
define('THEME_URL', ROOT_URL.'/Theme');

define('TEMPLATES_PATH', THEME_PATH.'/templates');
define('CSS_PATH', THEME_PATH.'/assets/css');
define('JS_PATH', THEME_PATH.'/assets/js');
define('IMAGES_PATH', THEME_PATH.'/assets/images');


define('TEMPLATES_URL', THEME_URL.'/templates');
define('CSS_URL', THEME_URL.'/assets/css');
define('JS_URL', THEME_URL.'/assets/js');
define('IMAGES_URL', THEME_URL.'/assets/images');


define('API_PATH', '/api');

define('ROOT_THEME_URL', ROOT_URL.THEME_PATH);
define('ROOT_API_URL', ROOT_URL.API_PATH);

define ('DOCUMENT_ROOT_API', DOCUMENT_ROOT.API_PATH);
define ('DOCUMENT_ROOT_THEME', DOCUMENT_ROOT.THEME_PATH);

define('SOCKET_PORT', 9010);

define('EMAIL', 'huetjeremy@outlook.com');
define('SMTP_HOST', 'smtp.orange.fr');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

ini_set('SMTP', SMTP_HOST);
ini_set('smtp_port', SMTP_PORT);
ini_set('username', SMTP_USERNAME);
ini_set('password', SMTP_PASSWORD);