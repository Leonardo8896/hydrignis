<?php
namespace Leonardo8896\Hydrignis\Model;

class GasAccident
{
    private ?string $completed = null;
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly string $time,
    ){}

    public function __serialize(): array
    {
        return [
            "id" => $this->id,
            "date" => $this->date,
            "time" => $this->time,
            "completed" => $this->completed
        ];
    }

    public function complete(): void 
    {
        $this->completed = date("Y-m-d H:i:s");
    }

}