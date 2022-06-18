<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Enum;

enum ConnectionState
{
    case CONNECTED;
    case CLOSED;
    case EXPIRED;

    public static function from(string $state): self
    {
        return match ($state) {
            "CONNECTED" => self::CONNECTED,
            "CLOSED" => self::CLOSED,
            "EXPIRED" => self::EXPIRED,
        };
    }
}