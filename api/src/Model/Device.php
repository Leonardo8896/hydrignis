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

    // Additional methods can be added here for device-specific logic
}