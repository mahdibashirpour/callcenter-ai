<?php

namespace App\Domain\Crm\Contracts;

use App\Domain\Crm\Enums\CrmLogStatus;
use App\Domain\Crm\Enums\CrmOperation;

interface CrmLogRepositoryInterface
{
    public function logConnection(
        int $connectionId,
        CrmOperation $operation,
        CrmLogStatus $status,
        ?array $request = null,
        ?array $response = null,
        ?string $message = null,
    ): void;

    public function logSync(
        int $connectionId,
        CrmOperation $operation,
        CrmLogStatus $status,
        ?array $payload = null,
        ?string $message = null,
        ?int $recordsProcessed = null,
    ): void;
}
