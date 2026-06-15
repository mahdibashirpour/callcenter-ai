<?php

namespace App\Domain\Voip\Contracts;

use App\Domain\Voip\DTOs\VoipConnectionConfig;
use Illuminate\Support\Collection;

interface VoipConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?VoipConnectionConfig;

    public function findForOrganization(int $organizationId, ?int $connectionId = null): ?VoipConnectionConfig;

    public function findDefaultForOrganization(int $organizationId): ?VoipConnectionConfig;

    public function allForOrganization(int $organizationId): Collection;
}
