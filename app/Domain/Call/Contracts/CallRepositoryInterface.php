<?php

namespace App\Domain\Call\Contracts;

use App\Domain\Call\DTOs\UnifiedCallData;

interface CallRepositoryInterface
{
    public function upsert(UnifiedCallData $data): int;

    public function findById(int $callId): ?UnifiedCallData;

    public function findByExternalId(int $organizationId, string $providerCode, string $externalCallId): ?UnifiedCallData;
}
