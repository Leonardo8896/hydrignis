<?php

namespace Leonardo8896\Hydrignis\Model;

class IgnisZeroDailyLog
{
    public function __construct(
        public readonly int $serialNumber,
        public readonly string $date,
        public readonly string $energyConsumption,
        private ?int $id = null,
    )
    {}

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "serial_number" => $this->serialNumber,
            "date" => $this->date,
            "energy_consumption" => $this->energyConsumption,
        ];
    }
}