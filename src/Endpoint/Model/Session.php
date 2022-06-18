<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Nerahikada\ValorantPHP\Endpoint\Enum\ConnectionState;
use Nerahikada\ValorantPHP\Endpoint\Enum\LoopState;

class Session
{
    private ConnectionState $connectionState;
    private string $clientId;
    private string $clientVersion;
    private LoopState $loopState;
    private string $loopStateMetadata;    // (pre-)match ID?
    private DateTimeImmutable $lastHeartbeatTime;
    private int $playtimeMinutes;
    private array $clientPlatformInfo;    // TODO: create model

    public function __construct(array $data)
    {
        $this->connectionState = ConnectionState::from($data["cxnState"]);
        $this->clientId = $data["clientID"];
        $this->clientVersion = $data["clientVersion"];
        $this->loopState = LoopState::from($data["loopState"]);
        $this->loopStateMetadata = $data["loopStateMetadata"];
        $this->lastHeartbeatTime = new DateTimeImmutable($data["lastHeartbeatTime"]);
        $this->playtimeMinutes = $data["playtimeMinutes"];
        $this->clientPlatformInfo = $data["clientPlatformInfo"];
    }

    public function getConnectionState(): ConnectionState
    {
        return $this->connectionState;
    }

    public function getClientId(): string
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

    public function getLoopStateMetadata(): string
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
