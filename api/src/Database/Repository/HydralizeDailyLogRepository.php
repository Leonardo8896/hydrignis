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
}