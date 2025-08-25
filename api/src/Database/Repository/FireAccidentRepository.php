<?php
namespace Leonardo8896\Hydrignis\Database\Repository;

use Leonardo8896\Hydrignis\Model\FireAccident;

class FireAccidentRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getFireAccidentsByDeviceSerial(string $serialNumber): array
    {
        $query = $this->pdo->prepare("SELECT * FROM FIRE_ACCIDENT WHERE IGNISZERO_device_serial_number = :serial_number");
        $query->bindParam(":serial_number", $serialNumber);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrateFireAccident'], $results);
    }

    public function getByLastDays(int $count, int $associative = false): array
    {
        $datePresent = new \DateTime(date('Y-m-d'));
        $lastDate = $datePresent->modify("-{$count} days");
        $dateString = $lastDate->format('Y-m-d');
        $query = $this->pdo->prepare("SELECT * FROM FIRE_ACCIDENT ORDER BY date DESC, time DESC WHERE date >= :date");
        $query->bindParam(":date", $dateString);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($associative) {
            return $results;
        }
        return array_map([$this, 'hydrateFireAccident'], $results);
    }

    private function hydrateFireAccident(array $data): FireAccident
    {
        return new FireAccident(
            id: $data['id'],
            date: $data['date'],
            time: $data['time']
        );
    }
}