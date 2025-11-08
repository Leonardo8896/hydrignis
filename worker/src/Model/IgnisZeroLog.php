<?php

namespace Leonardo8896\Hydrignis\Model;

class IgnisZeroLog
{
    public function __construct(
        public readonly string $timestamp,
        public readonly float $temperature,
        public readonly float $humidity,
        public readonly float $ph,
        public readonly float $ec
    ) 
    {
        
    }
}