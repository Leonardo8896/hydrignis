<?php 
namespace Leonardo8896\Hydrignis\Model;

class Hydralize extends Device
{
    public function __construct(
        string $serialNumber,
        string $name,
        string $location,
        string $type,
        ?string $last_connection = null,
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