<?php

namespace Hydrignis\Websocket\Model;

class IgnisZeroPlayload 
{
    private $bin;
    private ?array $image_array = null;
    private ?int $gas = null;
    private ?int $status = null;
    private ?int $onoff = null;

    public function __construct(string $bin)
    {
        if ($this->checkBegin($bin)) {
            $this->bin = $bin;
        } else {
            throw new \InvalidArgumentException("Payload inválido");
        }
    }

    private function checkBegin($bin): bool
    {
        return $bin[0] == "\xAA" && $bin[1] == "\x55";
    }

    public function dataLoad(): IgnisZeroPlayload
    {
        $playload = substr($this->bin, 2);
        $data = explode("\xAA\x55", $playload);
        // echo var_dump($data);
        // echo var_dump($this->bin);
        $this->image_array = $this->bin8ToArrayFloat($data[0]); 
        $this->gas = $this->bin8ToInt($data[1]);
        $this->status = $this->bin8ToInt($data[2]);
        $this->onoff = $this->bin8ToInt($data[3]);
        return $this;
    }

    private function bin8ToArrayFloat($bin): array|null
    {
        // if ($bin[0] !== "\xAA" || $bin[1] !== "\x55") {
        //     return null;
        // }

        // $playload = substr($bin, 2);

        // if (strlen($bin) !== $len*4) {
        //     return null;
        // }

        $frame = array_values(unpack('g*', $bin));
        echo var_dump($frame);
        return $frame;
    }

    private function bin8ToFloat($bin): float
    {
        $frame = unpack('g', $bin);
        echo var_dump($frame);
        return $frame[1];
    }

    private function bin8ToInt($bin): int
    {
        $frame = unpack('V', $bin);
        echo var_dump($frame);
        return $frame[1];
    }

    public function getIgnislog(): array|bool
    {
        if ($this->image_array == null && $this->gas == null && $this->status == null && $this->onoff == null) {
            return false;
        }
        $playload = [
            "Camera" => $this->image_array,
            "Gas" => $this->gas,
            "Status" => $this->status,
            "Onoff" => $this->onoff
        ];

        return $playload;
    }
}