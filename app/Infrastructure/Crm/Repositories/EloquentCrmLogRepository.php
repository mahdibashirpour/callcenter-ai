<?php

namespace App\Infrastructure\Crm\Repositories;

use App\Domain\Crm\Contracts\CrmLogRepositoryInterface;
use App\Domain\Crm\Enums\CrmLogStatus;
use App\Domain\Crm\Enums\CrmOperation;
use App\Models\CrmConnectionLog;
use App\Models\CrmSyncLog;

class EloquentCrmLogRepository implements CrmLogRepositoryInterface
{
    public function logConnection(
        int $connectionId,
        CrmOperation $operation,
        CrmLogStatus $status,
        ?array $request = null,
        ?array $response = null,
        ?string $message = null,
    ): void {
        CrmConnectionLog::query()->create([
            'organization_crm_connection_id' => $connectionId,
            'operation' => $operation->value,
            'status' => $status->value,
            'request_payload' => $request,
            'response_payload' => $response,
            'message' => $message,
        ]);
    }

    public function logSync(
        int $connectionId,
        CrmOperation $operation,
        CrmLogStatus $status,
        ?array $payload = null,
        ?string $message = null,
        ?int $recordsProcessed = null,
    ): void {
        CrmSyncLog::query()->create([
            'organization_crm_connection_id' => $connectionId,
            'operation' => $operation->value,
            'status' => $status->value,
            'payload' => $payload,
            'message' => $message,
            'records_processed' => $recordsProcessed,
        ]);
    }
}
