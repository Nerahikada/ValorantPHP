<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Nerahikada\ValorantPHP\Endpoint\Enum\AccountState;

class Account
{
    private AccountState $state;
    private string $uuid;
    private DateTimeImmutable $createdAt;
    private string $name;
    private string $tagLine;
    private string $country;
    private string $locale;

    public function __construct(array $data)
    {
        $this->state = AccountState::from($data["acct"]["state"]);
        $this->uuid = $data["sub"];
        $this->createdAt = new DateTimeImmutable("@{$data["acct"]["created_at"]}");
        $this->name = $data["acct"]["game_name"];
        $this->tagLine = $data["acct"]["tag_line"];
        $this->country = $data["country"];
        $this->locale = $data["player_locale"];
    }

    public function getState(): AccountState
    {
        return $this->state;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTagLine(): string
    {
        return $this->tagLine;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
