<?php
namespace Hydrignis\Websocket\Sockets;

use Hydrignis\Websocket\Model\IgnisPlayload;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WSBridge implements MessageComponentInterface {
    protected $clients;
    protected $mobiles;
    protected $devices;

    public function __construct() {
        $this->mobiles = new \SplObjectStorage;
        $this->devices = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);
        $key = $params["WS_KEY"];
        $type = $params["TYPE"];
        if ($key == $_ENV["KEY"]) {
            if($type == "device") {
                $this->devices->attach($conn);
                echo "New device ({$conn->resourceId})".PHP_EOL;
            } else if ($type == "mobile") {
                $this->mobiles->attach($conn);
                echo "New mobile ({$conn->resourceId})".PHP_EOL;
            } else {
                $conn->send("TYPE invalido.");
                $conn->close();
                echo "Connection refused";
            }
        } else {
            $conn->send("WS_KEY invalida.");
            $conn->close();
            echo "Connection refused";
        }
        // echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if ($this->devices->contains($from)) {
            // $array = array_values(unpack('f*',$msg));
            // var_dump(value: $array);
            // echo (strlen($msg)/4).PHP_EOL;
            $json = json_decode($msg);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($this->mobiles as $mobile) {
                    $mobile->send($json);
                    return;
                }
            }
            $ignis = new IgnisPlayload($msg);
            $playload = $ignis->getIgnislog();
            if (!$playload) {
                return;
            }
            // echo json_encode($playload).PHP_EOL;
            foreach ($this->mobiles as $mobile) {
                $mobile->send(json_encode($playload));
            }
            return;
        }

        if($this->mobiles->contains($from)) {
            foreach ($this->devices as $device) {
                $device->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        if ($this->devices->contains($conn)) {
            $this->devices->detach($conn);
            echo "Dropped connection device ({$conn->resourceId})".PHP_EOL;
            return;
        }

        if($this->mobiles->contains($conn)) {
            $this->mobiles->detach($conn);
            echo "Dropped connection mobile ({$conn->resourceId})".PHP_EOL;
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}