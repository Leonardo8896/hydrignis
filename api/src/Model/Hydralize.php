<?php 
namespace Leonardo8896\Hydrignis\Model;

class Hydralize extends Device
{
    public function __construct(
        string $serialNumber,
        string $name,
        string $last_connection,
        string $location,
        string $type,
        string $user_email,
        public readonly array $waterDailyLogs = []
    )
    {
        parent::__construct(
            $serialNumber,
            $name,
            $last_connection,
            $location,
            $type,
            $user_email
        );
    }
}