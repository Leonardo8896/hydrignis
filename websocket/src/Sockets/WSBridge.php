<?php
namespace Hydrignis\Websocket\Sockets;

use Hydrignis\Websocket\Model\HydralizePlayload;
use Hydrignis\Websocket\Model\IgnisZeroPlayload;
use Hydrignis\Websocket\Model\UserPlayload;
use Hydrignis\Websocket\Service\AccountService;
use Hydrignis\Websocket\Service\DeviceService;
use Leonardo8896\Hydrignis\Model\User;
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
        // echo var_dump($params);

        if (isset($params["serial_number"])) {
            // echo "teste";
            $linkResult = $this->linkDevice($params["serial_number"], $conn);
            if(!$linkResult) {
                $conn->send("Dispositivo não cadastrado ou dados incorretos.");
                $conn->close();
                echo "Connection refused".PHP_EOL;
                return;
            }
            return;
        }

        $auth = $params["token"];
        
        if(!$auth) {
            $conn->send("Token não fornecido.");
            $conn->close();
            echo "Connection refused".PHP_EOL;
            return;
        }
        //$token = str_replace('Bearer ', '', $auth[0] ?? '');

        $linkResult = $this->linkUser($auth, $conn);
        if (!$linkResult) {
            $conn->send("Token inválido ou expirado.");
            $conn->close();
            echo "Connection refused".PHP_EOL;
            return;
        }
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        // echo var_dump($msg);
        // echo var_dump(isset($this->meta[$from]));
        // echo $this->meta[$from];
        // echo "Message from {$from->resourceId}: ";
        if(isset($this->meta[$from])) {
            if (isset($this->meta[$from]["serial_number"])) {
                // echo "len=", strlen($msg),
                // " head=", bin2hex(substr($msg, 0, 4)),
                // " mark2@", 2 + 768*4, "=", bin2hex(substr($msg, 2 + 768*4, 2)),
                // PHP_EOL;

                // echo var_dump(bin2hex($msg));
                $users = $this->connections[$this->meta[$from]["user_email"]]["mobile"];

                // echo $users->count().PHP_EOL;
                foreach($users as $user) {
                    // echo var_dump($users[$user]);
                    try {
                        switch($this->meta[$from]["type"]) {
                            case "igniszero":
                                $playload = new IgnisZeroPlayload($msg);
                                break;
                            case "hydralize":
                                $playload = new HydralizePlayload($msg);
                                break;
                        }
                    } catch (\InvalidArgumentException $e) {
                        echo "Invalid payload: ".$e->getMessage().PHP_EOL;
                        return;
                    }
                    $decodedMsg = $playload->dataLoad()->getlog();
                    // [
                    //     "serial_number" => $this->meta[$from]["serial_number"],
                    //     $playload->dataLoad()->getlog()
                    // ];
                    // echo var_dump($decodedMsg);
                    $decodedMsg["serial_number"] = $this->meta[$from]["serial_number"];
                    $user->send(json_encode($decodedMsg));
                }
            } else {
                if (substr($msg, 0, 2) == "CK")
                try {
                    $playload = UserPlayload::load($msg);
                } catch (\InvalidArgumentException $e) {
                    $from->send("Invalid command ($msg)");
                    echo "Invalid payload: ".$e->getMessage().PHP_EOL;
                    return;
                }

                $device = $this->findDeviceBySerial($playload, $this->meta[$from]["user_email"]);
                if(!$device) {
                    $from->send("Device not found ({$playload->serialNumber})");
                    echo "Device not found ({$playload->serialNumber})".PHP_EOL;
                    return;
                }
                $device->send($playload->command);
            }
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $device = $this->meta[$conn];

        if(isset($device["type"])) {
            $this->connections[$device["user_email"]]["devices"][$device["type"]]->detach($conn);
            echo "Dropped connection from {$device["user_email"]} by device {$device["serial_number"]} ({$conn->resourceId})";
        } else {
            $this->connections[$device["user_email"]]["mobile"]->detach($conn);
            echo "Dropped connection from {$device["user_email"]} ({$conn->resourceId})";
        }

        $this->meta->detach($conn);

        // foreach ($this->meta as $connection) {
        //     echo var_dump($this->meta[$connection]);
        // }
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        // echo "An error has occurred: {$e->getMessage()}\n";

        // $conn->close();
    }

    private function findDeviceBySerial(UserPlayload $playload, string $user_email): ConnectionInterface|null
    {
        $devices = $this->connections[$user_email]["devices"][$playload->targetType];
        foreach($devices as $device) {
            // echo $devices[$device]["serial_number"].PHP_EOL;
            if($devices[$device]["serial_number"] == $playload->serialNumber) {
                return $device;
            }
        }

        return null;
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

    private function linkDevice(string $serialNumber, ConnectionInterface $conn): bool
    {
        $device = DeviceService::find($serialNumber);
        $owner = $device ? $device["USERS_email"] : false;
        $type = $device ? $device["type"] : false;


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
                break;
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
            "connected_at"=> $connectionTime,
            "type" => $type
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

    public function sendConnectedDevices(): void
    {
        // echo PHP_EOL."----------".PHP_EOL;
        foreach ($this->connections as $user_email => $connection) {
            $result = [
                "devices" => [],
                "mobile" => $connection["mobile"]->count()
            ];
            foreach ($connection["devices"] as $type => $devices) {
                foreach ($devices as $device) {
                    // echo var_dump($devices[$device]);
                    $result["devices"][] = [
                        "type" => $type,
                        "serial_number" => $devices[$device]["serial_number"],
                        "connected_at" => $devices[$device]["connected_at"]
                    ];
                }
            }

            foreach ($connection["mobile"] as $mobile) {
                $mobile->send(json_encode($result));
            }
        }
    }
}