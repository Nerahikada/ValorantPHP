<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Nerahikada\ValorantPHP\Endpoint\Enum\ConnectionState;
use Nerahikada\ValorantPHP\Endpoint\Enum\LoopState;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Session
{
    private ConnectionState $connectionState;
    private UuidInterface $clientId;
    private string $clientVersion;
    private LoopState $loopState;
    private ?UuidInterface $loopStateMetadata;    // (pre-)match ID?
    private DateTimeImmutable $lastHeartbeatTime;
    private int $playtimeMinutes;
    private array $clientPlatformInfo;    // TODO: create model

    public function __construct(array $data)
    {
        $this->connectionState = ConnectionState::from($data["cxnState"]);
        $this->clientId = Uuid::fromString($data["clientID"]);
        $this->clientVersion = $data["clientVersion"];
        $this->loopState = LoopState::from($data["loopState"]);
        $this->loopStateMetadata = empty($metadata = $data["loopStateMetadata"]) ? null : Uuid::fromString($metadata);
        $this->lastHeartbeatTime = new DateTimeImmutable($data["lastHeartbeatTime"]);
        $this->playtimeMinutes = $data["playtimeMinutes"];
        $this->clientPlatformInfo = $data["clientPlatformInfo"];
    }

    public function getConnectionState(): ConnectionState
    {
        return $this->connectionState;
    }

    public function getClientId(): UuidInterface
    {
        return $this->clientId;
    }

    public function getClientVersion(): string
    {
        return $this->clientVersion;
    }

    public function getLoopState(): LoopState
    {
        return $this->loopState;
    }

    public function getLoopStateMetadata(): UuidInterface
    {
        return $this->loopStateMetadata;
    }

    public function getLastHeartbeatTime(): DateTimeImmutable
    {
        return $this->lastHeartbeatTime;
    }

    public function getPlaytimeMinutes(): int
    {
        return $this->playtimeMinutes;
    }

    public function getClientPlatformInfo(): array
    {
        return $this->clientPlatformInfo;
    }
}
