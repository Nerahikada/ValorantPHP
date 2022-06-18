<?php

namespace Nerahikada\ValorantPHP\Endpoint\Model;

use Nerahikada\ValorantPHP\Endpoint\Enum\PlatformOs;
use Nerahikada\ValorantPHP\Endpoint\Enum\PlatformType;

class PlatformInfo
{
    private PlatformType $type;
    private PlatformOs $os;
    private string $version;
    private string $chipset;

    public function __construct(array $data)
    {
        $this->type = PlatformType::from($data["platformType"]);
        $this->os = PlatformOs::from($data["platformOS"]);
        $this->version = $data["platformOSVersion"];
        $this->chipset = $data["platformChipset"];
    }

    public function getType(): PlatformType
    {
        return $this->type;
    }

    public function getOs(): PlatformOs
    {
        return $this->os;
    }

    public function getOsVersion(): string
    {
        return $this->version;
    }

    public function getChipset(): string
    {
        return $this->chipset;
    }
}