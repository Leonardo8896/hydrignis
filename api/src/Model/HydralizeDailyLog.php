<?php

namespace Leonardo8896\Hydrignis\Model;

class HydralizeDailyLog
{
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly float $water_production,
        public readonly int $energy_consumption,
        public readonly int $battery_consumption,
    ){}
}