<?php

namespace Leonardo8896\Hydrignis\Model;

class HydralizeDailyLog
{
    public function __construct(
        public readonly int $serialNumber,
        public readonly string $date,
        public readonly float $water_production,
        public readonly int $energy_consumption,
        public readonly int $battery_consumption,
        private ?int $id = null,
    ){}

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "serial_number" => $this->serialNumber,
            "date" => $this->date,
            "water_production" => $this->water_production,
            "energy_consumption" => $this->energy_consumption,
            "battery_consumption" => $this->battery_consumption,
        ];
    }
}