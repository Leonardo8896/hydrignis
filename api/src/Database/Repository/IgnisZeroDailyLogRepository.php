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

    private function hydrateDailyLog(array $data): IgnisZeroDailyLog
    {
        return new IgnisZeroDailyLog(
            $data['id'],
            $data['date'],
            $data['energy_consumption']
        );
    }
}