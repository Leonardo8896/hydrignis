<?php

namespace Leonardo8896\Hydrignis\Database\Repository;
use Leonardo8896\Hydrignis\Model\HydralizeDailyLog;

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

    public function saveDailyLog(HydralizeDailyLog $dailyLog): ?HydralizeDailyLog
    {
        $query = $this->pdo->prepare("INSERT INTO HYDRALIZE_DAILY_LOG (HYDRALIZE_device_serial_number, date, water_consumption, energy_consumption, battery_consumption) VALUES (:serial_number, :date, :water_consumption, :energy_consumption, :battery_consumption)");
        $query->bindParam(":serial_number", $dailyLog->serialNumber);
        $query->bindParam(":date", $dailyLog->date);
        $query->bindParam(":water_consumption", $dailyLog->water_production);
        $query->bindParam(":energy_consumption", $dailyLog->energy_consumption);
        $query->bindParam(":battery_consumption", $dailyLog->battery_consumption);

        if ($query->execute()) {
            $id = (int)$this->pdo->lastInsertId();
            return new HydralizeDailyLog(
                $dailyLog->serialNumber,
                $dailyLog->date,
                $dailyLog->water_production,
                $dailyLog->energy_consumption,
                $dailyLog->battery_consumption,
                $id
            );
        }
        return null;

    }
}