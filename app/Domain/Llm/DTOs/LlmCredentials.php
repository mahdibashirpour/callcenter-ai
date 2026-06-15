<?php

namespace App\Domain\Llm\DTOs;

use App\Domain\Llm\Enums\LlmProviderCode;

readonly class LlmCredentials
{
    public function __construct(
        public ?string $apiKey = null,
        public ?string $apiSecret = null,
        public ?string $baseUrl = null,
        public ?string $organization = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            apiKey: $data['api_key'] ?? null,
            apiSecret: $data['api_secret'] ?? null,
            baseUrl: $data['base_url'] ?? null,
            organization: $data['organization'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'base_url' => $this->baseUrl,
            'organization' => $this->organization,
        ], fn ($value) => $value !== null);
    }
}
