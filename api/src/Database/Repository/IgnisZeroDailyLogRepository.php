<?php
namespace Leonardo8896\Hydrignis\Database\Repository;

use Leonardo8896\Hydrignis\Model\IgnisZeroDailyLog;

class IgnisZeroDailyLogRepository
{
    public function __construct(
        private \PDO $pdo
    ){}

    public function getDailyLogsByDeviceSerial(int $deviceSerial): array
    {
        $query = $this->pdo->prepare("SELECT * FROM IGNISZERO_DAILY_LOG WHERE IGNISZERO_device_serial_number = :serial_number");
        $query->bindParam(":serial_number", $deviceSerial);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, "hydrateDailyLog"], $results);
    }

    public function getLast(int $count, bool $associative = false): array
    {
        $presentDate = new \DateTime(date('Y-m-d'));
        $lastDate = $presentDate->modify("-{$count} days")->format('Y-m-d');
        $query = $this->pdo->prepare("SELECT * FROM IGNISZERO_DAILY_LOG WHERE date >= :date ORDER BY date DESC");
        $query->execute([":date" => $lastDate]);

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($associative) {
            return $results;
        }
        return array_map([$this, "hydrateDailyLog"], $results);
    }

    private function hydrateDailyLog(array $data): IgnisZeroDailyLog
    {
        return new IgnisZeroDailyLog(
            $data['id'],
            $data['date'],
            $data['energy_consumption']
        );
    }

    public function saveDailyLog(IgnisZeroDailyLog $data, string $user_email): ?IgnisZeroDailyLog
    {
        $serialNumber = $data->serialNumber;
        $date = $data->date;
        $energyConsumption = $data->energyConsumption;

        $query = $this->pdo->prepare("INSERT INTO IGNISZERO_DAILY_LOG (IGNISZERO_device_serial_number, date, energy_consumption, IGNISZERO_device_USERS_email) VALUES (:serial_number, :date, :energy_consumption, :user_email)");
        $query->bindParam(":serial_number", $serialNumber);
        $query->bindParam(":date", $date);
        $query->bindParam(":energy_consumption", $energyConsumption);
        $query->bindParam(":user_email", $user_email);
        
        if($query->execute()) {
            $id = (int)$this->pdo->lastInsertId();
            return new IgnisZeroDailyLog(
                $data->serialNumber,
                $data->date,
                $data->energyConsumption,
                $id
            );
        }
        return null;
    }
}