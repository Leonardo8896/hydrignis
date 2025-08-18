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
        string $last_connection,
        string $location,
        string $type,
        string $user_email,
        public readonly array $fireAccidents = [],
        public readonly array $dailyLogs = []
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