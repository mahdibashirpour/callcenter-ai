<?php

namespace App\Application\Call\Services;

use App\Domain\Call\Contracts\CallRepositoryInterface;
use App\Domain\Call\DTOs\UnifiedCallData;
use App\Models\VoipCallLog;

class CallIngestionService
{
    public function __construct(
        private CallRepositoryInterface $calls,
        private CallEmployeeResolver $employeeResolver,
    ) {}

    public function ingestFromVoipLog(VoipCallLog $log): int
    {
        $employeeId = $this->employeeResolver->resolveFromCallLog($log);

        return $this->calls->upsert(UnifiedCallData::fromVoipCallLog($log, $employeeId));
    }
}
