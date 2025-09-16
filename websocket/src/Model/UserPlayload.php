<?php

namespace Hydrignis\Websocket\Model;

class UserPlayload
{
    public function __construct(
        public readonly int $serialNumber,
        public string $command,
        public readonly string $targetType,
    ){}

    static public function load(string $msg): UserPlayload
    {
        $decoded = explode(";", $msg);
        // echo var_dump($decoded);
    
        if(count($decoded) != 3) {
            throw new \InvalidArgumentException("Payload inválido");
        }

        return new UserPlayload($decoded[0], $decoded[1], $decoded[2]);
    }
}