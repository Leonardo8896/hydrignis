<?php
namespace Hydrignis\Websocket\Sockets;

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
        $key = $conn->httpRequest->getHeaderLine("WS_KEY");
        $type = $conn->httpRequest->getHeaderLine("TYPE");
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
            foreach ($this->mobiles as $mobile) {
                $mobile->send($msg);
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