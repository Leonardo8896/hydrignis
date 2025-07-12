<?php
namespace Hydrignis\Websocket\Model;

class IgnisPlayload {
    private ?array $image_array = null;
    private ?int $gas = null;
    private ?int $status = null;
    private ?int $onoff = null;
    public function __construct($data) {
        $this->dataLoad($data);
    }

    private function dataLoad($bin): void
    {
        if ($bin[0] !== "\xAA" || $bin[1] !== "\x55") {
            return;
        }

        $playload = substr($bin, 2);

        $data = explode("\xAA\x55", $playload);
        $this->image_array = $this->bin8ToArrayFloat($data[0], 768);
        $this->gas = $this->bin8ToInt($data[1]);
        $this->status = $this->bin8ToInt($data[2]);
        $this->onoff = $this->bin8ToInt($data[3]);
    }
    private function bin8ToArrayFloat($bin, $len): array|null
    {
        // if ($bin[0] !== "\xAA" || $bin[1] !== "\x55") {
        //     return null;
        // }

        // $playload = substr($bin, 2);

        // if (strlen($bin) !== $len*4) {
        //     return null;
        // }

        $frame = array_values(unpack('g*', $bin));

        return $frame;
    }

    private function bin8ToFloat($bin): float
    {
        $frame = unpack('g', $bin);
        return $frame[1];
    }

    private function bin8ToInt($bin): int
    {
        $frame = unpack('V', $bin);
        return $frame[1];
    }

    public function getIgnislog(): array|bool
    {
        if ($this->image_array == null && $this->gas == null && $this->status == null && $this->onoff == null) {
            return false;
        }
        echo count($this->image_array);
        $playload = [
            "Camera" => $this->image_array,
            "Gas" => $this->gas,
            "Status" => $this->status,
            "Onoff" => $this->onoff
        ];

        return $playload;
    }
}