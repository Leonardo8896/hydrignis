<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use Leonardo8896\Hydrignis\Routine\DailyLogRoutine;
use Symfony\Component\Dotenv\Dotenv;

date_default_timezone_set('America/Sao_Paulo');

$path_env = __DIR__ . '/.env';
if (file_exists($path_env)) {
    $dotenv = new Dotenv();
    $dotenv->load($path_env);
}

$loop = Factory::create();
$loop->addPeriodicTimer(10, function() {
    echo date('H:i');
    if (date('H:i') != $_ENV['HYDRIGNIS_DAILY_LOG_TIME']) {
        return;
    }
    DailyLogRoutine::run();
});

$loop->run();