<?php

namespace App\Application\Crm\Services;

use App\Domain\Crm\Contracts\CrmAdapterInterface;
use App\Domain\Crm\Contracts\CrmConnectionRepositoryInterface;
use App\Domain\Crm\DTOs\CrmConnectionConfig;
use App\Domain\Crm\Exceptions\CrmConnectionNotFoundException;
use App\Infrastructure\Crm\CrmAdapterRegistry;

class CrmConnectionResolver
{
    public function __construct(
        private CrmConnectionRepositoryInterface $connections,
        private CrmAdapterRegistry $registry,
    ) {}

    /** @return array{0: CrmConnectionConfig, 1: CrmAdapterInterface} */
    public function resolve(int $organizationId, ?int $connectionId = null): array
    {
        $config = $connectionId
            ? $this->connections->findById($connectionId)
            : $this->connections->findDefaultForOrganization($organizationId);

        if (! $config || $config->organizationId !== $organizationId) {
            throw CrmConnectionNotFoundException::forOrganization($organizationId, $connectionId);
        }

        $adapter = $this->registry->resolve($config->providerCode);
        $adapter->configure($config);

        return [$config, $adapter];
    }
}
