<?php

namespace Leonardo8896\Hydrignis\Model;

class IgnisZeroDailyLog
{
    public function __construct(
        public readonly string $id,
        public readonly string $date,
        public readonly string $energyConsumption,
    )
    {}
}