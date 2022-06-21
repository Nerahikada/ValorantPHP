<?php

declare(strict_types=1);

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