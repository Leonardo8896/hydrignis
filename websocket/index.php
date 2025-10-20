<?php
require_once __DIR__ . '/vendor/autoload.php';

use Hydrignis\Websocket\Sockets\WSBridge;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Symfony\Component\Dotenv\Dotenv;

// Carregar .env se existir
$path_env = __DIR__ . '/.env';
if (file_exists($path_env)) {
    $dotenv = new Dotenv();
    $dotenv->load($path_env);
}

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

$loop = Factory::create();

$wsBridge = new WSBridge();
$ws = new WsServer($wsBridge);
$ws->setStrictSubProtocolCheck(false);

// Criar servidor HTTP + WebSocket
$http = new HttpServer($ws);
$socket = new SocketServer('0.0.0.0:9002', [], $loop);
$server = new IoServer($http, $socket, $loop);

// Timer periódico (2 segundos)
$loop->addPeriodicTimer(2, function() use ($wsBridge) {
    $wsBridge->sendConnectedDevices();
});

// Rodar tudo
$loop->run();
