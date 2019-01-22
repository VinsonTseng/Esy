<?php

// BASE_PATH
define('BASE_PATH', __DIR__);
// APP_PATH
define('APP_PATH', BASE_PATH . '/app');
// UPLOAD_PATH
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

// Autoload
$loader = require BASE_PATH . '/vendor/autoload.php';

// Bootstrap Loader
$bootstrap = new Esy\Bootstrap(require BASE_PATH . '/config/main.php');
$bootstrap->run();
