<?php

namespace Nerahikada\ValorantPHP\Endpoint\Enum;

enum PlatformType
{
    case PC;

    public static function from(string $type): self
    {
        return match ($type) {
            "PC" => self::PC,
        };
    }
}