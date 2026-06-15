<?php

namespace App\Domain\Voip\DTOs;

use App\Domain\Voip\Enums\VoipProviderCode;

readonly class VoipConnectionConfig
{
    public function __construct(
        public int $connectionId,
        public int $organizationId,
        public VoipProviderCode $providerCode,
        public string $name,
        public VoipCredentials $credentials,
        public VoipSettings $settings,
        public ?string $adapterClass = null,
        public bool $isDefault = false,
        public bool $isActive = true,
    ) {}

    public static function fromModel(object $connection): self
    {
        return new self(
            connectionId: $connection->id,
            organizationId: $connection->organization_id,
            providerCode: VoipProviderCode::from($connection->provider->code),
            name: $connection->name,
            credentials: VoipCredentials::fromArray($connection->credentials ?? []),
            settings: VoipSettings::fromArray($connection->settings ?? []),
            adapterClass: $connection->provider->adapter_class ?? null,
            isDefault: (bool) $connection->is_default,
            isActive: (bool) $connection->is_active,
        );
    }
}
