<?php

namespace App\Infrastructure\Llm\Repositories;

use App\Domain\Llm\Contracts\LlmConnectionRepositoryInterface;
use App\Domain\Llm\DTOs\LlmConnectionConfig;

class EloquentLlmConnectionRepository implements LlmConnectionRepositoryInterface
{
    public function findById(int $connectionId): ?LlmConnectionConfig
    {
        return null;
    }

    public function findDefaultForOrganization(int $organizationId): ?LlmConnectionConfig
    {
        return null;
    }
}
