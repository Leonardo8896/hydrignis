<?php

namespace Leonardo8896\Hydrignis\Model;

abstract class Device
{
    public readonly string $serial_number;
    public readonly array $logs;

    // abstract public function hydrateLogs(): string;
}