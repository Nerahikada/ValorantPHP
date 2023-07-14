<?php

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Restriction
{
    private string $type;   // TIME_BAN, PERMANENT_BAN, ...
    private string $reason; // GAMEPLAY_VIOLATION, AC_SCRIPTING_PERMANENT, ...
    private DateTimeImmutable $expiration;
    private ?UuidInterface $triggerGameId;

    public function __construct(array $data)
    {
        $this->type = $data["type"];
        $this->reason = $data["reason"];
        $this->expiration = new DateTimeImmutable("@" . ($data["dat"]["expirationMillis"] / 1000));
        $this->triggerGameId = isset($data["dat"]["gameData"]) ? Uuid::fromString($data["dat"]["gameData"]["triggerGameId"]) : null;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getExpiration(): DateTimeImmutable
    {
        return $this->expiration;
    }

    public function getTriggerGameId(): ?UuidInterface
    {
        return $this->triggerGameId;
    }
}