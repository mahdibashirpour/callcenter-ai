<?php

namespace App\Infrastructure\Llm\Repositories;

use App\Domain\Llm\Contracts\LlmLogRepositoryInterface;
use App\Domain\Llm\Enums\LlmLogStatus;
use App\Domain\Llm\Enums\LlmOperation;
use App\Models\LlmAnalysisLog;

class EloquentLlmLogRepository implements LlmLogRepositoryInterface
{
    public function logOperation(
        ?int $connectionId,
        LlmOperation $operation,
        LlmLogStatus $status,
        ?array $request = null,
        ?array $response = null,
        ?string $message = null,
        ?int $inputTokens = null,
        ?int $outputTokens = null,
        ?float $cost = null,
        ?int $durationMs = null,
    ): void {
        if ($connectionId === null) {
            return;
        }

        LlmAnalysisLog::query()->create([
            'organization_llm_connection_id' => $connectionId,
            'operation' => $operation,
            'status' => $status,
            'request_payload' => $request,
            'response_payload' => $response,
            'message' => $message,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'cost' => $cost,
            'duration_ms' => $durationMs,
        ]);
    }
}
