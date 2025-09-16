<?php

namespace Hydrignis\Websocket\Service;
use Hydrignis\Websocket\Database\Core\ConnectionCreator;

class DeviceService {
    static public function find(int $serialNumber): false|array
    {
        $pdo = ConnectionCreator::createPDOConnection();
        $query = $pdo->prepare("SELECT * FROM DEVICES WHERE serial_number = :serial_number");
        $query->bindValue(":serial_number", $serialNumber);
        $resultQuery = $query->execute();

        if(!$resultQuery) {
            echo "Error executing query.".PHP_EOL;
            return false;
        }

        $result = $query->fetch(\PDO::FETCH_ASSOC);
        // echo "Query result: " . var_export($result, true) . PHP_EOL;
        
        // Retorne o resultado ou false, dependendo do caso
        return $result ? $result : false;
    }
}