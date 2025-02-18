<?php

namespace App\Domain\Entity;

use DateTime;

class PersonalRecord
{
    private int $userId;
    private string $userName;
    private float $value;
    private DateTime $date;
    private int $position = 0;

    public function __construct(
        int $userId,
        string $userName,
        float $value,
        DateTime $date
    ) {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->value = $value;
        $this->date = $date;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function toArray(): array
    {
        return [
            'position' => $this->position,
            'user' => $this->userName,
            'value' => $this->value,
            'date' => $this->date->format('Y-m-d H:i:s')
        ];
    }
}