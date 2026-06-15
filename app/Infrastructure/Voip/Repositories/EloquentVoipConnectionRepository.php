<?php

namespace App\Infrastructure\Voip\Repositories;

use App\Domain\Voip\Contracts\VoipConnectionRepositoryInterface;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Models\OrganizationVoipConnection;
use Illuminate\Support\Collection;

class EloquentVoipConnectionRepository implements VoipConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?VoipConnectionConfig
    {
        $connection = OrganizationVoipConnection::query()
            ->with('provider')
            ->find($connectionId);

        return $connection ? VoipConnectionConfig::fromModel($connection) : null;
    }

    public function findForOrganization(int $organizationId, ?int $connectionId = null): ?VoipConnectionConfig
    {
        $query = OrganizationVoipConnection::query()
            ->with('provider')
            ->where('organization_id', $organizationId);

        if ($connectionId) {
            $query->whereKey($connectionId);
        } else {
            $query->where('is_default', true);
        }

        $connection = $query->first();

        return $connection ? VoipConnectionConfig::fromModel($connection) : null;
    }

    public function findDefaultForOrganization(int $organizationId): ?VoipConnectionConfig
    {
        return $this->findForOrganization($organizationId);
    }

    public function allForOrganization(int $organizationId): Collection
    {
        return OrganizationVoipConnection::query()
            ->with('provider')
            ->where('organization_id', $organizationId)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (OrganizationVoipConnection $connection) => VoipConnectionConfig::fromModel($connection));
    }
}
