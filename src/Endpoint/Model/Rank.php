<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

class Rank
{
    public function __construct(private readonly int $tier, private readonly int $rating)
    {
    }

    public function getTier(): int
    {
        return $this->tier;
    }

    public function getRating(): int
    {
        return $this->rating;
    }
}