<?php

namespace App\Infrastructure\Crm\Repositories;

use App\Domain\Crm\Contracts\CrmConnectionRepositoryInterface;
use App\Domain\Crm\DTOs\CrmConnectionConfig;
use App\Models\OrganizationCrmConnection;
use Illuminate\Support\Collection;

class EloquentCrmConnectionRepository implements CrmConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?CrmConnectionConfig
    {
        $connection = OrganizationCrmConnection::query()
            ->with('provider')
            ->find($connectionId);

        return $connection ? CrmConnectionConfig::fromModel($connection) : null;
    }

    public function findForOrganization(int $organizationId, ?int $connectionId = null): ?CrmConnectionConfig
    {
        $query = OrganizationCrmConnection::query()
            ->with('provider')
            ->where('organization_id', $organizationId);

        if ($connectionId) {
            $query->whereKey($connectionId);
        } else {
            $query->where('is_default', true);
        }

        $connection = $query->first();

        return $connection ? CrmConnectionConfig::fromModel($connection) : null;
    }

    public function findDefaultForOrganization(int $organizationId): ?CrmConnectionConfig
    {
        return $this->findForOrganization($organizationId);
    }

    public function allForOrganization(int $organizationId): Collection
    {
        return OrganizationCrmConnection::query()
            ->with('provider')
            ->where('organization_id', $organizationId)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (OrganizationCrmConnection $connection) => CrmConnectionConfig::fromModel($connection));
    }
}
