<?php

namespace Leonardo8896\Hydrignis\Controllers;

use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Leonardo8896\Hydrignis\Database\Repository\HydralizeDailyLogRepository;
use Leonardo8896\Hydrignis\Model\User;
use Leonardo8896\Hydrignis\Database\Repository\{
    DeviceRepository,
    FireAccidentRepository,
    GasAccidentRepository,
    IgnisZeroDailyLogRepository,
};
use Leonardo8896\Hydrignis\Model\FireAccident;


class DevicesController
{
    static public function index(User $user)
    {
        // Logic to handle device listing
        $deviceRepository = new DeviceRepository(ConnectionCreator::createPDOConnection());
        $devices = $deviceRepository->getDevicesByUserEmail($user->email);
        $devicesJson = array_map(function($device) {
            return $device->toArray();
        }, $devices);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'email' => $user->email,
                'name' => $user->name
            ],
            'devices' => $devicesJson
        ]);
        
    }

    static public function detailsIgnisZero(User $user)
    {
        $serialNumber = filter_input(INPUT_GET, 'serial_number');

        if (!$serialNumber) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Serial number is required']);
            return;
        }

        $fireAccidentRepository = new FireAccidentRepository(ConnectionCreator::createPDOConnection());
        $fireAccidents = $fireAccidentRepository->getFireAccidentsByDeviceSerial($serialNumber);

        $gasAccidentRepository = new GasAccidentRepository(ConnectionCreator::createPDOConnection());
        $gasAccidents = $gasAccidentRepository->getGasAccidentByDeviceSerial($serialNumber);

        $dailyLogsRepository = new IgnisZeroDailyLogRepository(ConnectionCreator::createPDOConnection());
        $dailyLogs = $dailyLogsRepository->getDailyLogsByDeviceSerial($serialNumber);

        $fireAccidentsArray = array_map(function($accidentF) {
            return $accidentF->toArray();
        }, $fireAccidents);
        $gasAccidentsArray = array_map(function($accidentG) {
            return $accidentG->toArray();
        }, $gasAccidents);
        $dailyLogsArray = array_map(function($log) {
            return $log->toArray();
        }, $dailyLogs);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'email' => $user->email,
                'name' => $user->name
            ],
            'fire_accidents' => $fireAccidentsArray,
            'gas_accidents' => $gasAccidentsArray,
            'daily_logs'=> $dailyLogs
        ]);
        
    }

    static function detailsHydralize(User $user)
    {
        // Logic to handle Hydralize device details
        $serialNumber = filter_input(INPUT_GET, 'serial_number');

        if (!$serialNumber) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Serial number is required']);
            return;
        }

        $hydralizeRepository = new HydralizeDailyLogRepository(ConnectionCreator::createPDOConnection());
        $dailyLogs = $hydralizeRepository->getDailyLogsByDeviceSerial($serialNumber);
        $dailyLogsArray = array_map(function($log) {
            return $log->toArray();
        }, $dailyLogs);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'email' => $user->email,
                'name' => $user->name
            ],
            'daily_logs' => $dailyLogsArray
        ]);
    }

    static function summaryDevices(User $user): void
    {
        $count = filter_input(INPUT_GET,'count', FILTER_SANITIZE_NUMBER_INT) ?? 10;

        $fireAccidentRepository = new FireAccidentRepository(ConnectionCreator::createPDOConnection());
        $fireAccidents = $fireAccidentRepository->getByLastDays($count, true);

        $gasAccidentRepository = new GasAccidentRepository(ConnectionCreator::createPDOConnection());
        $gasAccidents = $gasAccidentRepository->getByLastDays($count, true);

        $ignisZeroDailyLogRepository = new IgnisZeroDailyLogRepository(ConnectionCreator::createPDOConnection());
        $ignisZeroDailyLogs = $ignisZeroDailyLogRepository->getLast($count,true);

        $hydralizeDailyLogRepository = new HydralizeDailyLogRepository(ConnectionCreator::createPDOConnection());
        $hydralizeDailyLogs = $hydralizeDailyLogRepository->getLast($count, true);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'email' => $user->email,
                'name' => $user->name
            ],
            'fire_accidents' => $fireAccidents,
            'gas_accidents' => $gasAccidents,
            'igniszero_daily_logs' => $ignisZeroDailyLogs,
            'hydralize_daily_logs' => $hydralizeDailyLogs
        ]);
    }

    static function createDevice(User $user): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['serial_number']) || !isset($input['type']) || !isset($input['location']) || !isset($input['name'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Some information is missing']);
            return;
        }

        $serialNumber = $input['serial_number'];
        $type = $input['type'];
        $localtion = $input['location'];
        $name = $input['name'];

        $deviceRepository = new DeviceRepository(ConnectionCreator::createPDOConnection());
        $existingDevice = $deviceRepository->getDeviceBySerialNumber($serialNumber);

        if ($existingDevice) {
            http_response_code(409);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Device with this serial number already exists']);
            return;
        }

        $newDevice = $deviceRepository->createDevice($serialNumber, $name, $type, $localtion, $user->email);
        if ($newDevice) {
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'message' => 'Device created successfully',
                'device' => $newDevice->toArray()
            ]);
            return;
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to create device']);
            return;
        }
    }

    static function createFireAccident(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $date = $input['date'];
        $time = $input['time'];
        $serialNumber = $input['serial_number'];

        $fireAccident = new FireAccident($date, $time, $serialNumber);
        $fireAccidentRepository = new FireAccidentRepository(ConnectionCreator::createPDOConnection());
        if ($fireAccidentRepository->save($fireAccident)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Fire accident recorded successfully']);
            return;
        }

        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'An error occurred while recording the fire accident']);
    }
}