<?php 
namespace Leonardo8896\Hydrignis\Database\Repository;

use Leonardo8896\Hydrignis\Model\{
    IgnisZero,
    Hydralize,
    Device
};

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
            $data['last_connection'],
            $data['location'],
            $data['type']
        );
    }
}