<?php
require_once "../vendor/autoload.php";
require_once "../config/default-directories.php";

use Leonardo8896\Hydrignis\Controllers\Erro404Controller;
use Symfony\Component\Dotenv\Dotenv;

$enviroment = new Dotenv();
$enviroment->load(__DIR__.'/../.env');

$routes = require_once "../config/routes.php";
$requestPath = $_SERVER['PATH_INFO'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];

$key = "$requestPath|$requestMethod";
if (array_key_exists($key,$routes)) {
    call_user_func($routes[$key]);
} else {
    Erro404Controller::index();
}