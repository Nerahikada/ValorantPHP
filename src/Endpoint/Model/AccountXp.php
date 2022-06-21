<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

class AccountXp
{
    private int $level;
    private int $xp;
    private array $histroy;

    public function __construct(array $data)
    {
        $this->level = $data["Progress"]["Level"];
        $this->xp = $data["Progress"]["XP"];
        $this->histroy = array_reverse($data["History"]);// TODO: create model
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function getHistroy(): array
    {
        return $this->histroy;
    }
}