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

    public function getByLastDays(int $count, bool $associative = false): array
    {
        $datePresent = new \DateTime(date('Y-m-d'));
        $lastDate = $datePresent->modify("-{$count} days");
        $dateString = $lastDate->format('Y-m-d');
        $query = $this->pdo->prepare("SELECT * FROM FIRE_ACCIDENT WHERE date >= :date ORDER BY date DESC, time DESC");
        $query->bindParam(":date", $dateString);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($associative) {
            return $results;
        }
        return array_map([$this, 'hydrateFireAccident'], $results);
    }

    public function save(FireAccident $incident): bool
    {
        $query = $this->pdo->prepare("INSERT INTO FIRE_ACCIDENT (date, time, IGNISZERO_device_serial_number) VALUES (:date, :time, :serial_number)");
        $query->bindParam(":date", $incident->date);
        $query->bindParam(":time", $incident->time);
        $query->bindParam(":serial_number", $incident->serialNumber);
        return $query->execute();


        
    }

    private function hydrateFireAccident(array $data): FireAccident
    {
        return new FireAccident(
            id: $data['id'],
            date: $data['date'],
            time: $data['time'],
            serialNumber:$data['serial_number']
        );
    }
}