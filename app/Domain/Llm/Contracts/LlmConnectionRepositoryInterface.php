<?php

namespace App\Domain\Llm\Contracts;

use App\Domain\Llm\DTOs\LlmConnectionConfig;

interface LlmConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?LlmConnectionConfig;

    public function findDefaultForOrganization(int $organizationId): ?LlmConnectionConfig;
}
