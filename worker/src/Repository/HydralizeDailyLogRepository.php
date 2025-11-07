<?php

namespace Leonardo8896\Hydrignis\Repository;

class HydralizeDailyLogRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    public function saveHydralizeLog(string $sn, array $log, string $email): bool
    {
        echo "Saving log for SN: $sn\n";
        // return true;
        $query = $this->pdo->prepare("INSERT INTO HYDRALIZE_DAILY_LOG (date, water_production, energy_production, battery_consumption, HYDRALIZE_device_serial_number, HYDRALIZE_device_USERS_email) VALUES (:date, :water_production, :energy_production, :battery_consumption, :sn, :email)");
        $date = date('Y-m-d');
        $query->bindParam(":date", $date);
        $query->bindParam(":water_production", $log['water_production']);
        $query->bindParam(":energy_production", $log['energy_production']);
        $query->bindParam(":battery_consumption", $log['battery_consumption']);
        $query->bindParam(":sn", $sn);
        $query->bindParam(":email", $email);
        return $query->execute();
    }
}