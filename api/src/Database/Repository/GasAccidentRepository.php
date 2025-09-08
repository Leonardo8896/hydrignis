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

    public function getByLastDays(int $count, bool $associative = false): array
    {
        $datePresent = new \DateTime(date('Y-m-d'));
        $lastDate = $datePresent->modify("-{$count} days");
        $dateString = $lastDate->format('Y-m-d');
        $query = $this->pdo->prepare("SELECT * FROM GAS_ACCIDENT WHERE date >= :date ORDER BY date DESC, time DESC");
        $query->bindParam(":date", $dateString);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($associative) {
            return $results;
        }
        return array_map([$this, 'hydrateGasAccident'], $results);
    }

    public function hydrateGasAccident(array $data): GasAccident
    {
        return new GasAccident(
            id: $data['id'],
            date: $data['date'],
            time: $data['time'],
            serialNumber: $data['IGNISZERO_device_serial_number']
        );
    }

    public function save(GasAccident $gasAccident, string $user_email): bool
    {
        $date = $gasAccident->date;
        $time = $gasAccident->time;
        $serialNumber = $gasAccident->serialNumber;
        $query = $this->pdo->prepare("INSERT INTO GAS_ACCIDENT (IGNISZERO_device_serial_number, date, time, IGNISZERO_device_USERS_email) VALUES (:serial_number, :date, :time, :user_email)");
        $query->bindParam(":serial_number", $serialNumber  );
        $query->bindParam(":date", $date);
        $query->bindParam(":time", $time);
        $query->bindParam(":user_email", $user_email);
        return $query->execute();
    }
}