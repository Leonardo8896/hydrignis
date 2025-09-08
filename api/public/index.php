<?php
require_once "../vendor/autoload.php";
require_once "../config/default-directories.php";

use Leonardo8896\Hydrignis\Controllers\Erro404Controller;
use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Symfony\Component\Dotenv\Dotenv;
use Leonardo8896\Hydrignis\Service\AccountService;

date_default_timezone_set('America/Sao_Paulo');

$enviroment = new Dotenv();
$enviroment->load(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env');

$routes = require_once "../config/routes.php";
$requestPath = $_SERVER['PATH_INFO'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];
$header = getallheaders();

$key = "$requestPath|$requestMethod";
if (array_key_exists($key,$routes)) {
    if ($routes[$key]['auth'] && isset($header['Authorization'])) {
        $token = str_replace('Barier ', '', $header['Authorization']);
        $user = AccountService::checkToken($token);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido ou expirado']);
            exit;
        }
    }
    call_user_func($routes[$key]['handler'], $user ?? null);
} else {
    Erro404Controller::index();
}