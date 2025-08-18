<?php

namespace Leonardo8896\Hydrignis\Model;

class FireAccident
{
    public function __construct(
        public readonly string $id,
        public readonly string $date,
        public readonly string $time,
    ){}
}