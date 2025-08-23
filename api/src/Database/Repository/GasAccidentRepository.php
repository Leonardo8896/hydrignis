<?php

namespace Leonardo8896\Hydrignis\Database\Repository;
use Leonardo8896\Hydrignis\Model\GasAccident;

class GasAccidentRepository
{
    public function __construct(
        private $pdo
    ){}

    public function getGasAccidentByDeviceSerial(string $serialNumber): array
    {
        $query = $this->pdo->prepare("SELECT * FROM GAS_ACCIDENT WHERE IGNISZERO_device_serial_number = :serial_number");
        $query->bindParam(":serial_number", $serialNumber);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrateGasAccident'], $results);
    }

    public function hydrateGasAccident(array $data): GasAccident
    {
        return new GasAccident(
            id: $data['id'],
            date: $data['date'],
            time: $data['time']
        );
    }
}