<?php

namespace Hydrignis\Websocket\Model;

class IgnisZeroPlayload extends DevicePlayload
{
    private ?array $image_array = null;
    private ?int $gas = null;
    private ?int $status = null;
    private ?int $onoff = null;

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

    public function getlog(): array|bool
    {
        if ($this->image_array == null && $this->gas == null && $this->status == null && $this->onoff == null) {
            return false;
        }
        // echo var_dump($this->onoff);
        $playload = [
            "Camera" => $this->image_array,
            "Gas" => $this->gas,
            "Status" => $this->status,
            "Onoff" => $this->onoff
        ];

        return $playload;
    }
}