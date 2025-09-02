<?php

namespace Leonardo8896\Hydrignis\Model;

class FireAccident
{
    public function __construct(
        public readonly string $date,
        public readonly string $time,
        public readonly string $serialNumber,
        private ?string $id = null,
    ){}

    public function __get(string $name): ?string
    {
        if ($name === "id") {
            return $this->id;
        }
        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'serial_number'=> $this->serialNumber,
        ];
    }
}