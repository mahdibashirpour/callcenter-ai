<?php

namespace App\Domain\Crm\Contracts;

use App\Domain\Crm\DTOs\CrmConnectionConfig;
use Illuminate\Support\Collection;

interface CrmConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?CrmConnectionConfig;

    public function findForOrganization(int $organizationId, ?int $connectionId = null): ?CrmConnectionConfig;

    public function findDefaultForOrganization(int $organizationId): ?CrmConnectionConfig;

    public function allForOrganization(int $organizationId): Collection;
}
