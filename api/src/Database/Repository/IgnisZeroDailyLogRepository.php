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

    public function getGeneral(string $email): array
    {
        $query = $this->pdo->prepare(
            "SELECT 
                DATE_FORMAT(date, '%Y-%m') AS date, 
                IGNISZERO_device_USERS_email, 
                SUM(energy_consumption) AS total_energy,
                SUM(gas_accidents) AS total_gas_accidents,
                SUM(fire_accidents) AS total_fire_accidents
            FROM IGNISZERO_DAILY_LOG
            WHERE IGNISZERO_device_USERS_email = :email
            GROUP BY IGNISZERO_device_USERS_email, DATE_FORMAT(date, '%Y-%m')
            ORDER BY date;"
        );
        $query->bindParam(":email", $email);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        $result_array = [];
        foreach($results as $result) {
            $result_array[] = $this->hydrateGeneralDailyLog($result)->toArray();
        }
        return $result_array;
    }

    private function hydrateGeneralDailyLog(array $data): IgnisZeroDailyLog
    {
        return new IgnisZeroDailyLog(
            $data['date'],
            $data['energy_consumption'],
            $data['gas_accidents'],
            $data['fire_accidents']
        );
    }

    private function hydrateDailyLog(array $data): IgnisZeroDailyLog
    {
        return new IgnisZeroDailyLog(
            $data['date'],
            $data['energy_consumption'],
            $data['gas_accidents'],
            $data['fire_accidents'],
            $data['id'],
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
                $data->date,
                $data->energyConsumption,
                $data->gasAccidents,
                $data->fireAccidents,
                $id,
                $data->serialNumber,
            );
        }
        return null;
    }
}