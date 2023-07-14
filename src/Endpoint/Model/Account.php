<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use DateTimeImmutable;
use Nerahikada\ValorantPHP\Endpoint\Enum\AccountState;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Account
{
    private AccountState $state;
    private UuidInterface $uuid;
    private DateTimeImmutable $createdAt;
    private string $name;
    private string $tagLine;
    private string $country;
    private string $locale;
    /** @var Restriction[] */
    private array $restrictions;

    public function __construct(array $data)
    {
        $this->state = AccountState::from($data["acct"]["state"]);
        $this->uuid = Uuid::fromString($data["sub"]);
        $this->createdAt = new DateTimeImmutable("@{$data["acct"]["created_at"]}");
        $this->name = $data["acct"]["game_name"];
        $this->tagLine = $data["acct"]["tag_line"];
        $this->country = $data["country"];
        $this->locale = $data["player_locale"];
        $this->restrictions = array_map(fn($array) => new Restriction($array), $data["ban"]["restrictions"]);
    }

    public function getState(): AccountState
    {
        return $this->state;
    }

    public function getUuid(): UuidInterface
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

    /**
     * @return Restriction[]
     */
    public function getRestrictions(): array
    {
        return $this->restrictions;
    }
}
