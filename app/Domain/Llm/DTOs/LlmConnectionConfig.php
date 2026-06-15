<?php

namespace App\Domain\Llm\DTOs;

use App\Domain\Llm\Enums\LlmProviderCode;

readonly class LlmConnectionConfig
{
    public function __construct(
        public ?int $connectionId,
        public int $organizationId,
        public LlmProviderCode $providerCode,
        public string $name,
        public LlmCredentials $credentials,
        public LlmSettings $settings,
        public bool $isDefault,
        public bool $isActive,
    ) {}
}
