<?php

namespace Nerahikada\ValorantPHP\Endpoint\Enum;

enum PlatformOs
{
    case Windows;

    public static function from(string $os): self
    {
        return match ($os) {
            "Windows" => self::Windows,
        };
    }
}