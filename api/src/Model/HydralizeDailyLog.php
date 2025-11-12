<?php

namespace Leonardo8896\Hydrignis\Model;

class HydralizeDailyLog
{
    public function __construct(
        public readonly string $date,
        public readonly float $water_production,
        public readonly int $energy_production,
        public readonly int $battery_consumption,
        public readonly ?int $id = null,
        public readonly ?string $serialNumber = null
    ){}

    public function toArray(): array
    {
        $array = [
            "date" => $this->date,
            "water_production" => $this->water_production,
            "energy_production" => $this->energy_production,
            "battery_consumption" => $this->battery_consumption,
        ];

        if ($this->serialNumber !== null) {
            $array["serial_number"] = $this->serialNumber;
        }
        if ($this->id !== null) {
            $array["id"] = $this->id;
        }
        return $array;
    }
}