<?php

declare(strict_types=1);

namespace Nerahikada\ValorantPHP\Utils;

class CfCurlClient extends CurlClient
{
    public function __construct(bool $enableCookies = false)
    {
        parent::__construct($enableCookies);

        // Bypass cloudflare bot fight mode
        curl_setopt($this->curlHandle, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
        curl_setopt($this->curlHandle, CURLOPT_SSL_CIPHER_LIST, "ECDHE+AESGCM");
        $this->setUserAgent("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36");
        $this->addHeader("Accept-Language", "en");
    }
}