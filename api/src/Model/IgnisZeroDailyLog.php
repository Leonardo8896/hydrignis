<?php

namespace Leonardo8896\Hydrignis\Model;

class IgnisZeroDailyLog
{
    public function __construct(
        public readonly string $date,
        public readonly string $energyConsumption,
        public readonly int $gasAccidents,
        public readonly int $fireAccidents,
        private ?int $id = null,
        public readonly ?string $serialNumber = null
    )
    {}

    public function toArray(): array
    {
        $array = [
            "date" => $this->date,
            "energy_consumption" => $this->energyConsumption,
            "gas_accidents" => $this->gasAccidents,
            "fire_accidents" => $this->fireAccidents,
        ];

        if ($this->id !== null) {
            $array["id"] = $this->id;
        }
        if ($this->serialNumber !== null) {
            $array["serial_number"] = $this->serialNumber;
        }

        return $array;
    }
}