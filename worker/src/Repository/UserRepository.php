<?php

namespace Leonardo8896\Hydrignis\Repository;
use Leonardo8896\Hydrignis\Model\User;

class UserRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    public function getUserWithLogs(string $path): User|false
    {
        if(file_exists(__DIR__."/tmp/".$email.".tmp")) {
            $data = file_get_contents(__DIR__."/tmp/".$email.".tmp");
            $obj = json_decode($data);
            $logs = [];
            
            foreach($obj["logs"] as $sn => $log) {
                $i = 0;
                $log = [];
                foreach($log as $entry) {
                    foreach($entry as $key => $value) {
                        if(!isset($log[$key])) {
                            $log[$key] = 0;
                            continue;
                        }
                        $log[$key] += $value;
                    }
                }
                foreach($log as $key => $value) {
                    $log[$key] = $value / $i;
                }
                $logs[$sn] = $log;
            }


            return new User($email, $logs);
        } else {
            return false;
        }
    }

    public function saveLogs(User $user): void
    {
        $logs = $user->logs;
        foreach($logs as $sn => $log) {
            $this->saveHydralizeLog($sn, $log, $user->email);
        }
    }

    private function saveHydralizeLog(string $sn, array $log, string $email): void
    {
        $query = $this->pdo->prepare("INSERT INTO HYDRALIZE_DAILY_LOG (date, water_production, energy_production, battery_consumption, HYDRALIZE_device_serial_number, HYDRALIZE_device_USERS_email) VALUES (:email, :sn, :email)");
        $query->bindParam(":water_production", $log['water_production']);
        $query->bindParam(":energy_production", $log['energy_production']);
        $query->bindParam(":battery_consumption", $log['battery_consumption']);
        $query->bindParam(":sn", $sn);
        $log_data = json_encode($log);
        $query->bindParam(":email", $email);
        $query->execute();
    }
}