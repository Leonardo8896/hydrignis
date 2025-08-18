<?php 
namespace Leonardo8896\Hydrignis\Database\Repository;

class DeviceRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    // Example method to fetch devices for a user
    public function getDevicesByUserId(int $email): array
    {
        $query = $this->pdo->prepare("SELECT * FROM DEVICES WHERE USERS_email = :user_email");
        $query->bindParam(":user_email", $email);
        $query->execute();
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDeviceBySerialNumber(string $serialNumber): array|null
    {
        $query = $this->pdo->prepare("SELECT * FROM DEVICES WHERE serial_number = :serial_number");
        $query->bindParam(":serial_number", $serialNumber);
        $query->execute();
        
        if ($query->rowCount() === 0) {
            return null;
        }
        
        return $query->fetch(\PDO::FETCH_ASSOC);
    }
}