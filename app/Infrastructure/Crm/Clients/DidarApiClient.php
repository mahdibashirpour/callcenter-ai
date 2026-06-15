<?php

namespace App\Infrastructure\Crm\Clients;

use App\Domain\Crm\DTOs\CrmCredentials;
use App\Domain\Crm\DTOs\CrmSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class DidarApiClient
{
    public function __construct(
        private CrmCredentials $credentials,
        private CrmSettings $settings,
    ) {}

    public function post(string $endpoint, array $payload = []): Response
    {
        return $this->request()->post($this->buildUrl($endpoint), $payload);
    }

    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request()->get($this->buildUrl($endpoint), $query);
    }

    private function request(): PendingRequest
    {
        return Http::timeout($this->settings->timeout)
            ->acceptJson()
            ->asJson();
    }

    private function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->credentials->apiUrl, '/');
        $endpoint = ltrim($endpoint, '/');
        $url = "{$baseUrl}/{$endpoint}";

        $apiKey = $this->credentials->authKey();
        if ($apiKey) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= "{$separator}apikey={$apiKey}";
        }

        return $url;
    }
}
