<?php 

require_once __DIR__."/vendor/autoload.php";

use Hydrignis\Websocket\Sockets\WSBridge;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Dotenv\Dotenv;

$path_env = __DIR__."/.env";
if (file_exists($path_env)){
    $dotenv = new Dotenv();
    $dotenv->load($path_env);
}

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

$server = IoServer::factory(new HttpServer(new WsServer(new WSBridge())), 8000);
$server->run();