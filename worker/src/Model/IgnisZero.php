<?php

namespace Leonardo8896\Hydrignis\Model;
use Leonardo8896\Hydrignis\Model\Device;

class IgnisZero extends Device
{
    public readonly array $logs;
    
    public function __construct(
        public readonly string $serial_number,
        $logs = []
    ) 
    {
        
    }

    public function hydrateLogs(): string
    {
        
    }
}