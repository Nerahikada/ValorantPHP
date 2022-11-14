<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Utils;

use CurlHandle;
use Nerahikada\ValorantPHP\Exception\CurlRequestFailedException;

class CurlClient
{
    protected CurlHandle $curlHandle;
    private /*resource*/ $tmpCookie;

    /** @var string[] */
    private array $headers = [];

    public function __construct(bool $enableCookies = false)
    {
        $this->curlHandle = curl_init();
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_AUTOREFERER, true);

        if ($enableCookies) {
            $cookie = stream_get_meta_data($this->tmpCookie = tmpfile())["uri"];
            curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($this->curlHandle, CURLOPT_COOKIEFILE, $cookie);
        }
    }

    protected function setUserAgent(string $useragent): void
    {
        curl_setopt($this->curlHandle, CURLOPT_USERAGENT, $useragent);
    }

    protected function addHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    protected function removeHeader(string $key): void
    {
        unset($this->headers[$key]);
    }

    protected function postJson(string $url, array $contents = null): string
    {
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $contents ? json_encode($contents) : null);
        return $this->sendRequest("POST", $url, ["Content-Type: application/json"]);
    }

    private function sendRequest(string $method, string $url, array $headers = []): bool|string
    {
        foreach ($this->headers as $key => $value) $headers[] = "$key: $value";

        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($this->curlHandle);
        $code = curl_getinfo($this->curlHandle, CURLINFO_RESPONSE_CODE);
        if ((int)($code / 100) !== 2) throw new CurlRequestFailedException((string)$response, $code);

        return $response;
    }

    protected function putJson(string $url, array $contents = null): string
    {
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $contents ? json_encode($contents) : null);
        return $this->sendRequest("PUT", $url, ["Content-Type: application/json"]);
    }

    protected function get(string $url, array $query = null): string
    {
        if (!is_null($query)) $url .= "?" . http_build_query($query);
        $response = $this->sendRequest("GET", $url);
        $code = curl_getinfo($this->curlHandle, CURLINFO_RESPONSE_CODE);

        if ($code !== 200) throw new CurlRequestFailedException((string)$response, $code);

        return $response;
    }

    protected function patchJson(string $url, array $contents = null): string
    {
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $contents ? json_encode($contents) : null);
        return $this->sendRequest("PATCH", $url, ["Content-Type: application/json"]);
    }
}
