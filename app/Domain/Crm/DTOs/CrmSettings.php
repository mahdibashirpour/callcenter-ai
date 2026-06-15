<?php

namespace App\Domain\Crm\DTOs;

readonly class CrmSettings
{
    public function __construct(
        public ?string $webhookUrl = null,
        public ?string $webhookSecret = null,
        public int $timeout = 30,
        public array $extra = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            webhookUrl: $data['webhook_url'] ?? $data['webhookUrl'] ?? null,
            webhookSecret: $data['webhook_secret'] ?? $data['webhookSecret'] ?? null,
            timeout: (int) ($data['timeout'] ?? 30),
            extra: $data['extra'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'webhook_url' => $this->webhookUrl,
            'webhook_secret' => $this->webhookSecret,
            'timeout' => $this->timeout,
            'extra' => $this->extra ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
