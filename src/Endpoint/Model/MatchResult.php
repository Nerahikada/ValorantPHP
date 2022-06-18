<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MatchResult
{
    private UuidInterface $id;
    private string $mapId;
    private UuidInterface $seasonId;
    private DateTimeImmutable $startTime;
    private Rank $newRank;
    private Rank $oldRank;
    private int $earnedRating;
    private int $bonusRating;
    private int $afkPenalty;

    public function __construct(array $data)
    {
        $this->id = Uuid::fromString($data["MatchID"]);
        $this->mapId = $data["MapID"];
        $this->seasonId = Uuid::fromString($data["SeasonID"]);
        $this->startTime = new DateTimeImmutable("@{$data["MatchStartTime"]}");
        $this->newRank = new Rank($data["TierAfterUpdate"], $data["RankedRatingAfterUpdate"]);
        $this->oldRank = new Rank($data["TierBeforeUpdate"], $data["RankedRatingBeforeUpdate"]);
        $this->earnedRating = $data["RankedRatingEarned"];
        $this->bonusRating = $data["RankedRatingPerformanceBonus"];
        $this->afkPenalty = $data["AFKPenalty"];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getMapId(): string
    {
        return $this->mapId;
    }

    public function getSeasonId(): UuidInterface
    {
        return $this->seasonId;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getNewRank(): Rank
    {
        return $this->newRank;
    }

    public function getOldRank(): Rank
    {
        return $this->oldRank;
    }

    public function getEarnedRating(): int
    {
        return $this->earnedRating;
    }

    public function getBonusRating(): int
    {
        return $this->bonusRating;
    }

    public function getAfkPenalty(): int
    {
        return $this->afkPenalty;
    }
}