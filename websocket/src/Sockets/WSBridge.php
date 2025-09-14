<?php
namespace Hydrignis\Websocket\Sockets;

use Hydrignis\Websocket\Model\IgnisZeroPlayload;
use Hydrignis\Websocket\Service\AccountService;
use Hydrignis\Websocket\Service\DeviceOwnerService;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsConnection;

class WSBridge implements MessageComponentInterface {
    private array $connections = [];
    private \SplObjectStorage $meta;

    public function __construct() {
        $this->meta = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn): void 
    {
        echo "New connection request".PHP_EOL;

        // Verificando os parâmetros da query
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);
        echo var_dump($params);

        if (isset($params["serial_number"]) && isset($params["type"])) {
            // echo "teste";
            $linkResult = $this->linkDevice($params["serial_number"], $params["type"], $conn);
            if(!$linkResult) {
                $conn->send("Dispositivo não cadastrado ou dados incorretos.");
                $conn->close();
                echo "Connection refused".PHP_EOL;
                return;
            }
            return;
        }

        $auth = $conn->httpRequest->getHeader("Authorization");
        if(!$auth) {
            $conn->send("Token não fornecido.");
            $conn->close();
            echo "Connection refused".PHP_EOL;
            return;
        }
        $token = str_replace('Bearer ', '', $auth[0] ?? '');
        $linkResult = $this->linkUser($token, $conn);
        if (!$linkResult) {
            $conn->send("Token inválido ou expirado.");
            $conn->close();
            echo "Connection refused".PHP_EOL;
            return;
        }
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        echo var_dump($msg);
        // echo var_dump(isset($this->meta[$from]));
        // if(isset($this->meta[$from])) {
        //     $users = $this->connections[$this->meta[$from]["user_email"]]["mobile"];
        //     echo $users->count().PHP_EOL;
        //     foreach($users as $user) {
        //         echo var_dump($users[$user]);
        //         try {
        //             $playload = new IgnisZeroPlayload($msg);
        //         } catch (\InvalidArgumentException $e) {
        //             echo "Invalid payload: ".$e->getMessage().PHP_EOL;
        //             return;
        //         }
        //         $decodedMsg = $playload->dataLoad()->getIgnislog();
        //         echo var_dump($decodedMsg);
        //         $user->send($decodedMsg);
        //     }
        // }
        // if ($this->devices->contains($from)) {
        //     // $array = array_values(unpack('f*',$msg));
        //     // var_dump(value: $array);
        //     // echo (strlen($msg)/4).PHP_EOL;
        //     $json = json_decode($msg);
        //     if (json_last_error() === JSON_ERROR_NONE) {
        //         foreach ($this->mobiles as $mobile) {
        //             $mobile->send($json);
        //             return;
        //         }
        //     }
        //     $ignis = new IgnisPlayload($msg);
        //     $playload = $ignis->getIgnislog();
        //     if (!$playload) {
        //         return;
        //     }
        //     // echo json_encode($playload).PHP_EOL;
        //     foreach ($this->mobiles as $mobile) {
        //         $mobile->send(json_encode($playload));
        //     }
        //     return;
        // }

        // if($this->mobiles->contains($from)) {
        //     foreach ($this->devices as $device) {
        //         $device->send($msg);
        //     }
        // }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        // The connection is closed, remove it, as we can no longer send it messages
        // if ($this->devices->contains($conn)) {
        //     $this->devices->detach($conn);
        //     echo "Dropped connection device ({$conn->resourceId})".PHP_EOL;
        //     return;
        // }

        // if($this->mobiles->contains($conn)) {
        //     $this->mobiles->detach($conn);
        //     echo "Dropped connection mobile ({$conn->resourceId})".PHP_EOL;
        // }
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        // echo "An error has occurred: {$e->getMessage()}\n";

        // $conn->close();
    }

    private function createConnection(string $user_email): void
    {
        $this->connections[$user_email] = [
            "devices" => [
                "igniszero" => new \SplObjectStorage,
                "hydralize" => new \SplObjectStorage
            ],
            "mobile" => new \SplObjectStorage
        ];
    }

    private function linkDevice(string $serialNumber, string $type, ConnectionInterface $conn): bool
    {
        $owner = DeviceOwnerService::find($serialNumber);

        if (!$owner) {
            return false;
        }

        if(!isset($this->connections[$owner])) {
            $this->createConnection($owner);
        }

        $connectionTime = date("Y-m-d H:i:s");

        switch ($type) {
            case "igniszero":
                $this->connections[$owner]["devices"]["igniszero"][$conn] = [
                    "serial_number" => $serialNumber,
                    "connected_at" => $connectionTime
                ];
            case "hydralize":
                $this->connections[$owner]["devices"]["hydralize"][$conn] = [
                    "serial_number" => $serialNumber,
                    "connected_at" => $connectionTime
                ];
                break;
            default:
                return false;
        }

        $this->meta[$conn] = [
            "user_email"=> $owner,
            "serial_number" => $serialNumber,
            "connected_at"=> $connectionTime
        ];

        echo "New device connection from {$owner} - {$type} ({$conn->resourceId})".PHP_EOL;
        return true;
    }

    private function linkUser(string $token, ConnectionInterface $conn): bool
    {
        $claims = AccountService::decodeToken($token);
        if (!$claims) {
            // $conn->send("Token inválido ou expirado.");
            // $conn->close();
            // echo "Connection refused".PHP_EOL;
            return false;
        }

        $email = $claims["user_email"];

        if(!isset($this->connections[$email])) {
            $this->createConnection($email);
        }

        $connectionTime = date("Y-m-d H:i:s");
        $this->connections[$email]["mobile"][$conn] = [
            "user_email"=> $email,
            "connected_at"=> $connectionTime
        ];

        $this->meta[$conn] = [
            "user_email"=> $email,
            "connected_at"=> $connectionTime
        ];

        echo "New mobile connection from {$email} ({$conn->resourceId})".PHP_EOL;
        return true;
    }
}