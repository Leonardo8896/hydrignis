<?php

namespace Leonardo8896\Hydrignis\Database\Repository;

class IgnisZeroDailyLogRepository
{
    public function __construct(
        private \PDO $pdo
    ){}

    public function saveIgnisZeroLog(string $sn, array $log, string $email): bool
    {
        echo "Saving log for SN: $sn\n";
        // return true;
        $query = $this->pdo->prepare("INSERT INTO IGNISZERO_DAILY_LOG (date, energy_consumption, gas_accidents, fire_accidents, IGNISZERO_device_serial_number, IGNISZERO_device_USERS_email) VALUES (:date, :energy_consumption, :gas_accidents, :fire_accidents, :sn, :email)");
        $date = date('Y-m-d');
        $query->bindParam(":date", $date);
        $query->bindParam(":energy_consumption", $log['energy_consumption'], \PDO::PARAM_INT);
        $query->bindParam(":gas_accidents", $log['gas_accidents'], \PDO::PARAM_INT);
        $query->bindParam(":fire_accidents", $log['fire_accidents'], \PDO::PARAM_INT);
        $query->bindParam(":sn", $sn);
        $query->bindParam(":email", $email);
        return $query->execute();
    }
}