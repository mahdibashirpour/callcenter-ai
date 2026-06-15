<?php

namespace App\Application\Voip\Services;

use App\Domain\Voip\Contracts\VoipAdapterInterface;
use App\Domain\Voip\Contracts\VoipConnectionRepositoryInterface;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Domain\Voip\Exceptions\VoipConnectionNotFoundException;
use App\Infrastructure\Voip\VoipAdapterRegistry;

class VoipConnectionResolver
{
    public function __construct(
        private VoipConnectionRepositoryInterface $connections,
        private VoipAdapterRegistry $registry,
    ) {}

    /** @return array{0: VoipConnectionConfig, 1: VoipAdapterInterface} */
    public function resolve(int $organizationId, ?int $connectionId = null): array
    {
        $config = $connectionId
            ? $this->connections->findById($connectionId)
            : $this->connections->findDefaultForOrganization($organizationId);

        if (! $config || $config->organizationId !== $organizationId) {
            throw VoipConnectionNotFoundException::forOrganization($organizationId, $connectionId);
        }

        $adapter = $this->registry->resolve($config->providerCode, $config->adapterClass);
        $adapter->configure($config);

        return [$config, $adapter];
    }

    public function resolveByConnectionId(int $connectionId): array
    {
        $config = $this->connections->findById($connectionId);

        if (! $config) {
            throw VoipConnectionNotFoundException::forOrganization(0, $connectionId);
        }

        $adapter = $this->registry->resolve($config->providerCode, $config->adapterClass);
        $adapter->configure($config);

        return [$config, $adapter];
    }
}
