<?php
namespace Leonardo8896\Hydrignis\Model;

class GasAccident
{
    private ?string $completed = null;
    public function __construct(
        public readonly string $date,
        public readonly string $time,
        public readonly string $serialNumber,
        private ?string $id = null,
    ){}

    public function __get(string $name): ?string
    {
        if ($name === "id") {
            return $this->id;
        }
        return null;
    }

    public function toArray(): array
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