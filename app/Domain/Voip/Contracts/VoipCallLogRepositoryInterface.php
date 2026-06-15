<?php

namespace App\Domain\Voip\Contracts;

use App\Domain\Voip\DTOs\CallLogData;

interface VoipCallLogRepositoryInterface
{
    public function upsert(CallLogData $data): void;

    public function findByExternalCallId(int $connectionId, string $externalCallId): ?object;
}
