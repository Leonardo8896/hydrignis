<?php

namespace Leonardo8896\Hydrignis\Database\Repository;
use Leonardo8896\Hydrignis\Model\HydralizeDailyLog;
use Leonardo8896\Hydrignis\Model\User;

class HydralizeDailyLogRepository
{
    public function __construct(
        private \PDO $pdo
    ){}

    public function getDailyLogsByDeviceSerial(int $deviceSerial): array
    {
        $query = $this->pdo->prepare("SELECT * FROM HYDRALIZE_DAILY_LOG WHERE HYDRALIZE_device_serial_number = :serial_number");
        $query->bindParam(":serial_number", $deviceSerial);
        $query->execute();

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, "hydrateDailyLog"], $results);
    }

    public function getLast(int $count, bool $associative = false): array
    {
        $presentDate = new \DateTime(date('Y-m-d'));
        $lastDate = $presentDate->modify("-{$count} days")->format('Y-m-d');
        $query = $this->pdo->prepare("SELECT * FROM HYDRALIZE_DAILY_LOG WHERE date >= :date ORDER BY date DESC");
        $query->execute([":date" => $lastDate]);

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($associative) {
            return $results;
        }
        return array_map([$this, "hydrateDailyLog"], $results);
    }

    private function hydrateDailyLog(array $data): HydralizeDailyLog
    {
        return new HydralizeDailyLog(
            $data['id'],
            $data['date'],
            $data['water_consumption'],
            $data['energy_consumption'],
            $data['battery_consumption'],
        );
    }

    public function saveDailyLog(HydralizeDailyLog $dailyLog, User $user): ?HydralizeDailyLog
    {
        $serialNumber = $dailyLog->serialNumber;
        $date = $dailyLog->date;
        $waterProduction = $dailyLog->water_production;
        $energyProduction = $dailyLog->energy_production;
        $batteryConsumption = $dailyLog->battery_consumption;
        $user_email = $user->email;

        $query = $this->pdo->prepare("INSERT INTO HYDRALIZE_DAILY_LOG (HYDRALIZE_device_serial_number, date, water_production, energy_production, battery_consumption, HYDRALIZE_device_USERS_email) VALUES (:serial_number, :date, :water_production, :energy_production, :battery_consumption, :user_email)");
        $query->bindParam(":serial_number", $serialNumber);
        $query->bindParam(":date", $date);
        $query->bindParam(":water_production", $waterProduction);
        $query->bindParam(":energy_production", $energyProduction);
        $query->bindParam(":battery_consumption", $batteryConsumption);
        $query->bindParam(":user_email", $user_email);

        if ($query->execute()) {
            $id = (int)$this->pdo->lastInsertId();
            return new HydralizeDailyLog(
                $dailyLog->serialNumber,
                $dailyLog->date,
                $dailyLog->water_production,
                $dailyLog->energy_production,
                $dailyLog->battery_consumption,
                $id
            );
        }
        return null;

    }
}