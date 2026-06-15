<?php

namespace App\Domain\Voip\DTOs;

readonly class VoipCredentials
{
    public function __construct(
        public string $apiUrl,
        public ?string $apiKey = null,
        public ?string $apiToken = null,
        public ?string $username = null,
        public ?string $password = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            apiUrl: $data['api_url'] ?? $data['apiUrl'] ?? '',
            apiKey: $data['api_key'] ?? $data['apiKey'] ?? null,
            apiToken: $data['api_token'] ?? $data['apiToken'] ?? null,
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'api_url' => $this->apiUrl,
            'api_key' => $this->apiKey,
            'api_token' => $this->apiToken,
            'username' => $this->username,
            'password' => $this->password,
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function authToken(): ?string
    {
        return $this->apiKey ?? $this->apiToken;
    }
}
