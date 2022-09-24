<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP;

use DateInterval;
use DateTimeImmutable;
use Nerahikada\ValorantPHP\Endpoint\Model\Account;
use Nerahikada\ValorantPHP\Endpoint\Model\AccountXp;
use Nerahikada\ValorantPHP\Endpoint\Model\MatchResult;
use Nerahikada\ValorantPHP\Endpoint\Model\Session;
use Nerahikada\ValorantPHP\Exception\AuthenticationFailureException;
use Nerahikada\ValorantPHP\Exception\CurlRequestFailedException;
use Nerahikada\ValorantPHP\Exception\UnderMaintenanceException;
use Nerahikada\ValorantPHP\Utils\CurlClient;

class ValorantClient extends CurlClient
{
    private DateTimeImmutable $expiredAt;
    private string $region;
    private Account $account;

    public function __construct()
    {
        parent::__construct(true);
        $this->setUserAgent("RiotClient/56.0.0.4578455.4552318 rso-auth (Windows;10;;Professional, x64)");
    }

    public function login(string $username, string $password): bool
    {
        $this->postJson("https://auth.riotgames.com/api/v1/authorization", [
            "client_id" => "play-valorant-web-prod",
            "response_type" => "token id_token",
            "redirect_uri" => "https://playvalorant.com/opt_in",
            "scope" => "account openid",
            "nonce" => "1",
        ]);

        $response = $this->putJson("https://auth.riotgames.com/api/v1/authorization", [
            "type" => "auth",
            "username" => $username,
            "password" => $password,
            "remember" => true,
        ]);

        $response = json_decode($response, true);
        $response = match ($response["type"]) {
            "response" => $response["response"],
            "multifactor" => throw new AuthenticationFailureException("2FA Authentication is not supported"),
            "auth" => throw new AuthenticationFailureException($response["error"]),
        };

        parse_str(parse_url($response["parameters"]["uri"])[$response["mode"]], $result);
        $this->expiredAt = (new DateTimeImmutable())->add(new DateInterval("PT{$result["expires_in"]}S"));
        $this->addHeader("Authorization", "{$result["token_type"]} {$result["access_token"]}");

        $response = $this->putJson("https://riot-geo.pas.si.riotgames.com/pas/v1/product/valorant", ["id_token" => $result["id_token"]]);
        $this->region = json_decode($response, true)["affinities"]["live"];

        $response = $this->postJson("https://entitlements.auth.riotgames.com/api/token/v1");
        $this->addHeader("X-Riot-Entitlements-JWT", json_decode($response, true)["entitlements_token"]);

        $this->account = new Account(json_decode($this->postJson("https://auth.riotgames.com/userinfo"), true));

        /** @link https://github.com/techchrism/valorant-api-docs/blob/trunk/docs/common-components.md#client-platform */
        $this->addHeader("X-Riot-ClientPlatform", "ew0KCSJwbGF0Zm9ybVR5cGUiOiAiUEMiLA0KCSJwbGF0Zm9ybU9TIjogIldpbmRvd3MiLA0KCSJwbGF0Zm9ybU9TVmVyc2lvbiI6ICIxMC4wLjE5MDQyLjEuMjU2LjY0Yml0IiwNCgkicGxhdGZvcm1DaGlwc2V0IjogIlVua25vd24iDQp9");

        return true;
    }

    /**
     * @return MatchResult[]
     */
    public function fetchMatches(int $count = 1, string $game = "competitive"): array
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid();
        try {
            $response = $this->get("https://pd.$region.a.pvp.net/mmr/v1/players/$puuid/competitiveupdates", [
                "endIndex" => $count,
                "queue" => $game,
            ]);
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        return array_map(fn(array $data) => new MatchResult($data), json_decode($response, true)["Matches"]);
    }

    private function reauth(): void
    {
        if ((new DateTimeImmutable())->add(new DateInterval("PT10S")) < $this->expiredAt) return;

        try {
            $this->get("https://auth.riotgames.com/authorize", [
                "client_id" => "play-valorant-web-prod",
                "response_type" => "token id_token",
                "redirect_uri" => "https://playvalorant.com/opt_in",
                "nonce" => "1",
            ]);
        } catch (CurlRequestFailedException $exception) {
            if ($exception->getCode() === 303) {
                parse_str(parse_url(substr($exception->getMessage(), 26))["fragment"], $result);
                $this->expiredAt = (new DateTimeImmutable())->add(new DateInterval("PT{$result["expires_in"]}S"));
                $this->addHeader("Authorization", "{$result["token_type"]} {$result["access_token"]}");
                return;
            }
            throw $exception;
        }
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function checkMaintenance(CurlRequestFailedException $exception): void
    {
        $response = json_decode($exception->getMessage());
        if (is_array($response)) {
            if (isset($data["errorCode"]) && $data["errorCode"] === "SCHEDULED_DOWNTIME") {
                throw new UnderMaintenanceException($data["message"]);
            }
        }
    }

    public function fetchLoadout(): array
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid();
        try {
            $response = $this->get("https://pd.$region.a.pvp.net/personalization/v2/players/$puuid/playerloadout");
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        return json_decode($response, true);    // TODO: create model
    }

    public function fetchAccountXp(): AccountXp
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid();
        try {
            $response = $this->get("https://pd.$region.a.pvp.net/account-xp/v1/players/$puuid");
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        return new AccountXp(json_decode($response, true));
    }

    public function fetchMmr(): array
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid();
        try {
            $response = $this->get("https://pd.$region.a.pvp.net/mmr/v1/players/$puuid");
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        // TODO: create model
        return json_decode($response, true);
    }

    public function fetchParty(): array
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid()->toString();
        try {
            $this->addHeader("X-Riot-ClientVersion", $this->fetchSession()->getClientVersion());
            $response = $this->get("https://glz-$region-1.$region.a.pvp.net/parties/v1/players/$puuid");
            $this->removeHeader("X-Riot-ClientVersion");
            $id = json_decode($response, true)["CurrentPartyID"];
            $response = $this->get("https://glz-$region-1.$region.a.pvp.net/parties/v1/parties/$id");
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        // TODO: create model
        return json_decode($response, true);
    }

    public function fetchSession(): ?Session
    {
        $this->reauth();

        $region = $this->region;
        $puuid = $this->getAccount()->getUuid();
        try {
            $response = $this->get("https://glz-$region-1.$region.a.pvp.net/session/v1/sessions/$puuid");
        } catch (CurlRequestFailedException $exception) {
            if ($exception->getCode() === 404) return null;
            $this->checkMaintenance($exception);
            throw $exception;
        }
        return new Session(json_decode($response, true));
    }

    public function fetchPenalties(): array
    {
        $this->reauth();
        try {
            $response = $this->get("https://pd.$this->region.a.pvp.net/restrictions/v3/penalties");
        } catch (CurlRequestFailedException $exception) {
            $this->checkMaintenance($exception);
            throw $exception;
        }
        // TODO: create model
        return json_decode($response, true)["Penalties"];
    }
}
