<?php

namespace Leonardo8896\Hydrignis\Routine;
use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Leonardo8896\Hydrignis\Repository\{HydralizeDailyLogRepository};

/*
Estrutura padrão de arquivo de log
{
    email: string,
    logs: {
        [serial_number: string]: [
            {
                water_production: float,
                energy_production: float,
                battery_consumption: float
            },
            ...
        ]
    }
}
*/

class DailyLogRoutine
{
    public static function run(): void
    {
        echo "Running Daily Log Routine...\n";
        $hydralizeDailyLogRepository = new HydralizeDailyLogRepository(ConnectionCreator::createPDOConnection());
        $tmp_dir = scandir(__DIR__ . "/../../..".$_ENV['HYDRIGNIS_DAILY_LOG_DIR']);

        if(count($tmp_dir) == 2) {
            echo "No log files found.\n";
            return;
        }

        foreach($tmp_dir as $file) {
            if($file === "." || $file === "..") {
                continue;
            }
            $data = file_get_contents(__DIR__ . "/../../..".$_ENV['HYDRIGNIS_DAILY_LOG_DIR']."/".$file);
            echo $data.PHP_EOL;
            $obj = json_decode($data, true);
            echo var_dump($obj);
            $email = $obj["email"];
            foreach($obj["logs"] as $sn => $log) {
                $i = 0;
                $aggregatedLog = [];
                foreach($log as $entry) {
                    foreach($entry as $key => $value) {
                        if(!isset($aggregatedLog[$key])) {
                            $aggregatedLog[$key] = 0;
                        }
                        $aggregatedLog[$key] += $value;
                    }
                    $i++;
                }
                foreach($aggregatedLog as $key => $value) {
                    $aggregatedLog[$key] = $value / $i;
                }
                $result = $hydralizeDailyLogRepository->saveHydralizeLog($sn, $aggregatedLog, $email);
                if (!$result) {
                    echo "Failed to save log for SN: $sn, Email: $email\n";
                } else {
                    echo "Successfully saved log for SN: $sn, Email: $email\n";
                }
            }
            unlink(__DIR__ . "/../../..".$_ENV['HYDRIGNIS_DAILY_LOG_DIR']."/".$file);
        }
    }
}