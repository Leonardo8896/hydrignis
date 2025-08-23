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
        public readonly array $waterDailyLogs = []
    )
    {
        parent::__construct(
            $serialNumber,
            $name,
            $location,
            $type,
            $last_connection
        );
    }
}