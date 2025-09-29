<?php

namespace Hydrignis\Websocket\Model;

use Hydrignis\Websocket\Model\DevicePlayload;

class HydralizePlayload extends DevicePlayload
{
    private int $onOff;
    private int $waterInput;
    private int $waterOut;
    private int $battery;

    public function dataLoad(): HydralizePlayload
    {
        $playload = substr($this->bin, 2);
        $data = explode("\xAA\x55", $playload);
        
        $this->onOff = $this->bin8ToInt($data[0]);
        $this->waterInput = $this->bin8ToInt($data[1]);
        $this->waterOut = $this->bin8ToInt($data[2]);
        $this->battery = $this->bin8ToInt($data[3]);

        return $this;
    }

    function getLog(): array|bool
    {
        $playload = [
            "onOff" => $this->onOff,
            "waterInput" => $this->waterInput,
            "waterOut" => $this->waterOut,
            "battery" => $this->battery
        ];

        return $playload;
    }
}