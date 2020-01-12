<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__);
define('APP_PATH', __DIR__. DS . '..' . DS . 'app');

require_once __DIR__.'/../app/init.php';
$app = new app;

