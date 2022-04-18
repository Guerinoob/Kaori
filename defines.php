<?php
define ('DOCUMENT_ROOT', __DIR__.'');

define('CLASSES_DIR', DOCUMENT_ROOT.'/Classes');
define('OVERRIDE_DIR', DOCUMENT_ROOT.'/Override');
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

define('ROOT_THEME_URL', ROOT_URL.THEME_PATH);
define('ROOT_API_URL', ROOT_URL.API_PATH);

define ('DOCUMENT_ROOT_API', DOCUMENT_ROOT.API_PATH);
define ('DOCUMENT_ROOT_THEME', DOCUMENT_ROOT.THEME_PATH);

ini_set('SMTP', SMTP_HOST);
ini_set('smtp_port', SMTP_PORT);
ini_set('username', SMTP_USERNAME);
ini_set('password', SMTP_PASSWORD);