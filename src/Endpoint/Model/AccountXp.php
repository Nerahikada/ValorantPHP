<?php

namespace Nerahikada\ValorantPHP\Endpoint\Model;

class AccountXp
{
    private int $level;
    private int $xp;

    public function __construct(array $data)
    {
        $this->level = $data["Progress"]["Level"];
        $this->xp = $data["Progress"]["XP"];

        // TODO:
        //$history = array_reverse($data["History"]);
    }
}