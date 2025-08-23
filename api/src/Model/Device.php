<?php

namespace Leonardo8896\Hydrignis\Model;

abstract class Device
{
    public function __construct(
        public readonly string $serialNumber,
        public readonly string $name,
        public readonly string $location,
        public readonly string $type,
        public readonly ?string $last_connection = null,
    ) {}

    public function toArray(): array
    {
        return [
            'serial_number' => $this->serialNumber,
            'name' => $this->name,
            'last_connection' => $this->last_connection,
            'location' => $this->location,
            'type' => $this->type
        ];
    }
}