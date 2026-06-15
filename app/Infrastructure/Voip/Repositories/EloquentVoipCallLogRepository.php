<?php

namespace App\Infrastructure\Voip\Repositories;

use App\Domain\Voip\Contracts\VoipCallLogRepositoryInterface;
use App\Domain\Voip\DTOs\CallLogData;
use App\Models\VoipCallLog;

class EloquentVoipCallLogRepository implements VoipCallLogRepositoryInterface
{
    public function upsert(CallLogData $data): void
    {
        VoipCallLog::query()->updateOrCreate(
            [
                'organization_voip_connection_id' => $data->connectionId,
                'external_call_id' => $data->externalCallId,
            ],
            $data->toArray(),
        );
    }

    public function findByExternalCallId(int $connectionId, string $externalCallId): ?object
    {
        return VoipCallLog::query()
            ->where('organization_voip_connection_id', $connectionId)
            ->where('external_call_id', $externalCallId)
            ->first();
    }
}
