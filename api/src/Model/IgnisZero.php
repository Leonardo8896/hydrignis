<?php
namespace Leonardo8896\Hydrignis\Model;

use Leonardo8896\Hydrignis\Model\{
    Device, 
    FireAccident, 
    IgnisZeroDailyLog
};

class IgnisZero extends Device
{
    public function __construct(
        string $serialNumber,
        string $name,
        string $location,
        string $type,
        ?string $last_connection = null,
        public readonly array $fireAccidents = [],
        public readonly array $dailyLogs = []
    )
    {
        parent::__construct(
            $serialNumber,
            $name,
            $location,
            $type,
            $last_connection,
        );
    }
}