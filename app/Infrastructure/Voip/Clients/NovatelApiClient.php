<?php

namespace App\Infrastructure\Voip\Clients;

use App\Domain\Voip\DTOs\VoipCredentials;
use App\Domain\Voip\DTOs\VoipSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NovatelApiClient
{
    public function __construct(
        private VoipCredentials $credentials,
        private VoipSettings $settings,
    ) {}

    public function post(string $endpoint, array $payload = []): Response
    {
        return $this->request()->post($this->buildUrl($endpoint), $payload);
    }

    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request()->get($this->buildUrl($endpoint), $query);
    }

    public function put(string $endpoint, array $payload = []): Response
    {
        return $this->request()->put($this->buildUrl($endpoint), $payload);
    }

    public function delete(string $endpoint, array $payload = []): Response
    {
        return $this->request()->delete($this->buildUrl($endpoint), $payload);
    }

    private function request(): PendingRequest
    {
        $request = Http::timeout($this->settings->timeout)
            ->acceptJson()
            ->asJson();

        if ($token = $this->credentials->authToken()) {
            $request = $request->withToken($token);
        }

        if ($this->credentials->username && $this->credentials->password) {
            $request = $request->withBasicAuth(
                $this->credentials->username,
                $this->credentials->password,
            );
        }

        return $request;
    }

    private function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->credentials->apiUrl, '/');
        $endpoint = ltrim($endpoint, '/');

        return "{$baseUrl}/{$endpoint}";
    }
}
