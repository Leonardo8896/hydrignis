<?php 
namespace Leonardo8896\Hydrignis\Database\Repository;

use Leonardo8896\Hydrignis\Model\{
    IgnisZero,
    Hydralize,
    Device
};

use Leonardo8896\Hydrignis\Model\User;

class DeviceRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    // Example method to fetch devices for a user
    public function getDevicesByUserEmail(string $email): array
    {
        $query = $this->pdo->prepare("SELECT * FROM DEVICES WHERE USERS_email = :user_email");
        $query->bindParam(":user_email", $email);
        $query->execute();
        
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(function($data) {
            if ($data['type'] == "igniszero") {
                return $this->hydrateIgnisZero($data);
            } else if ($data['type'] == 'hydralize') {
                return $this->hydrateHydralize($data);
            }
        }, $results);
    }

    public function getDeviceBySerialNumber(string $serialNumber): Device|null
    {
        $query = $this->pdo->prepare("SELECT * FROM DEVICES WHERE serial_number = :serial_number");
        $query->bindParam(":serial_number", $serialNumber);
        $query->execute();
        
        if ($query->rowCount() === 0) {
            return null;
        }

        $deviceData = $query->fetch(\PDO::FETCH_ASSOC);
        if ($deviceData['type'] == "igniszero") {
            $device = $this->hydrateIgnisZero($deviceData);
        } else if ($deviceData['type'] == 'hydralize') {
            $device = $this->hydrateHydralize($deviceData);
        }
        
        return $device;
    }

    private function hydrateIgnisZero(array $data): IgnisZero
    {
        // var_dump($data);
        // die;
        return new IgnisZero(
            $data['serial_number'],
            $data['name'],
            $data['location'],
            $data['type'],
            $data['last_connection']
        );
    }

    private function hydrateHydralize(array $data): Hydralize
    {
        // Assuming Hydralize is another class similar to IgnisZero
        return new Hydralize(
            $data['serial_number'],
            $data['name'],
            $data['location'],
            $data['type']
        );
    }

    public function createDevice(Device $device, User $user): Device|bool
    {
        $name = $device->name;
        $location = $device->location;
        $type = $device->type;
        $serialNumber = $device->serialNumber;
        $userEmail = $user->email;

        $query = $this->pdo->prepare("INSERT INTO DEVICES (serial_number, name, type, location, USERS_email) VALUES (:serial_number, :name, :type, :location, :user_email)");
        $query->bindParam(":serial_number", $serialNumber);
        $query->bindParam(":name", $name);
        $query->bindParam(":type", $type);
        $query->bindParam(":location", $location);
        $query->bindParam(":user_email", $userEmail);
        $result = $query->execute();

        if($result) {
            if ($type == "igniszero") {
                $query2 = $this->pdo->prepare("INSERT INTO IGNISZERO (device_serial_number, device_USERS_email) VALUES (:serial_number, :user_email)");
                $query2->bindParam(":serial_number", $serialNumber);
                $query2->bindParam(":user_email", $userEmail);
                if (!$query2->execute()) {
                    // If the second insert fails, you might want to rollback the first insert
                    $this->pdo->prepare("DELETE FROM DEVICES WHERE serial_number = :serial_number")
                        ->execute([':serial_number' => $serialNumber]);
                    return false;
                }

                return $this->hydrateIgnisZero([
                    'serial_number' => $serialNumber,
                    'name' => $name,
                    'type' => $type,
                    'location' => $location,
                    'last_connection' => null
                ]);
            } else if ($type == 'hydralize') {
                $query2 = $this->pdo->prepare('INSERT INTO HYDRALIZE (device_serial_number, device_USERS_email) VALUES (:serial_number, :user_email)');
                $query2->bindParam(':serial_number', $serialNumber);
                $query2->bindParam(':user_email', $userEmail);
                if (!$query2->execute()) {
                    // If the second insert fails, you might want to rollback the first insert
                    $this->pdo->prepare('DELETE FROM DEVICES WHERE serial_number = :serial_number')
                        ->execute([':serial_number' => $serialNumber]);
                    return false;
                }

                return $this->hydrateHydralize([
                    'serial_number' => $serialNumber,
                    'name' => $name,
                    'type' => $type,
                    'location'=> $location,
                    'last_connection'=> null
                ]);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


}