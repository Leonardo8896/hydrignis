<?php

namespace Leonardo8896\Hydrignis\Model;

abstract class Device
{
    public function __construct(
        public readonly string $serialNumber,
        public readonly string $name,
        public readonly string $last_connection,
        public readonly string $location,
        public readonly string $type,
        public readonly string $user_email
    ) {}

    public function __serialize(): array
    {
        return [
            'serial_number' => $this->serialNumber,
            'name' => $this->name,
            'last_connection' => $this->last_connection,
            'location' => $this->location,
            'type' => $this->type,
            'user_email' => $this->user_email
        ];
    }
}