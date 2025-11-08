<?php

namespace Leonardo8896\Hydrignis\Model;

class Hydralize extends Device
{
    public function __construct(
        public readonly string $serial_number,
        public readonly array $logs
    ) 
    {}
}