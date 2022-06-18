<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Enum;

enum LoopState
{
    case MENUS;
    case PREGAME;
    case INGAME;

    public static function from(string $state): self
    {
        return match ($state) {
            "MENUS" => self::MENUS,
            "PREGAME" => self::PREGAME,
            "INGAME" => self::INGAME,
        };
    }
}