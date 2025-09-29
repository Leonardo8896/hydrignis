<?php

namespace Hydrignis\Websocket\Model;

abstract class DevicePlayload
{
    protected $bin;
    public function __construct(string $bin)
    {
        if ($this->checkBegin($bin)) {
            $this->bin = $bin;
        } else {
            throw new \InvalidArgumentException("Payload inválido");
        }
    }

    protected function checkBegin($bin): bool
    {
        return $bin[0] == "\xAA" && $bin[1] == "\x55";
    }

    abstract public function dataLoad(): DevicePlayload;

    protected function bin8ToArrayFloat($bin): array|null
    {
        // if ($bin[0] !== "\xAA" || $bin[1] !== "\x55") {
        //     return null;
        // }

        // $playload = substr($bin, 2);

        // if (strlen($bin) !== $len*4) {
        //     return null;
        // }

        $frame = array_values(unpack('g*', $bin));
        // echo var_dump($frame);
        return $frame;
    }

    protected function bin8ToFloat($bin): float
    {
        $frame = unpack('g', $bin);
        // echo var_dump($frame);
        return $frame[1];
    }

    protected function bin8ToInt($bin): int
    {
        $frame = unpack('V', $bin);
        // echo var_dump($frame);
        return $frame[1];
    }

    abstract public function getLog() :array|bool;
}