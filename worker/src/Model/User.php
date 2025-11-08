<?php

namespace Leonardo8896\Hydrignis\Model;

class User
{
    public function __construct(
        public readonly string $email,
        public readonly array $logs
    ) 
    {}


}