<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Enum;

enum AccountState
{
    case ENABLED;
    // TODO: find out more...

    public static function from(string $state): self
    {
        return match ($state) {
            "ENABLED" => self::ENABLED,
        };
    }
}