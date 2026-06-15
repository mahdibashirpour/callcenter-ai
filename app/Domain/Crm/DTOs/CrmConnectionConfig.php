<?php

namespace App\Domain\Crm\DTOs;

use App\Domain\Crm\Enums\CrmProviderCode;

readonly class CrmConnectionConfig
{
    public function __construct(
        public int $connectionId,
        public int $organizationId,
        public CrmProviderCode $providerCode,
        public string $name,
        public CrmCredentials $credentials,
        public CrmSettings $settings,
        public bool $isDefault = false,
        public bool $isActive = true,
    ) {}

    public static function fromModel(object $connection): self
    {
        return new self(
            connectionId: $connection->id,
            organizationId: $connection->organization_id,
            providerCode: CrmProviderCode::from($connection->provider->code),
            name: $connection->name,
            credentials: CrmCredentials::fromArray($connection->credentials ?? []),
            settings: CrmSettings::fromArray($connection->settings ?? []),
            isDefault: (bool) $connection->is_default,
            isActive: (bool) $connection->is_active,
        );
    }
}
