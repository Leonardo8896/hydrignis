<?php

namespace Leonardo8896\Hydrignis\Controllers;

use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Leonardo8896\Hydrignis\Model\User;
use Leonardo8896\Hydrignis\Database\Repository\DeviceRepository;

class DevicesController
{
    static public function index(User $user)
    {
        // Logic to handle device listing
        $deviceRepository = new DeviceRepository(ConnectionCreator::createPDOConnection());
        $devices = $deviceRepository->getDevicesByUserEmail($user->email);
        $devicesJson = array_map(function($device) {
            return serialize($device);
        }, $devices);

        http_response_code(200);
        echo json_encode([
            'user' => [
                'email' => $user->email,
                'name' => $user->name
            ],
            'devices' => $devicesJson
        ]);
        
    }

    static public function device(User $user)
    {
        $serialNumber = filter_input(INPUT_GET, 'serial_number');

        if (!$serialNumber) {
            http_response_code(400);
            echo json_encode(['error' => 'Serial number is required']);
            return;
        }

        $deviceRepository = new DeviceRepository(ConnectionCreator::createPDOConnection());
        $device = $deviceRepository->getDeviceBySerialNumber($serialNumber);

        
    }
}