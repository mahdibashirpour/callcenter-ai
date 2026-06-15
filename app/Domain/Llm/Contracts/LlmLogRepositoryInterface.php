<?php

namespace App\Domain\Llm\Contracts;

use App\Domain\Llm\Enums\LlmLogStatus;
use App\Domain\Llm\Enums\LlmOperation;

interface LlmLogRepositoryInterface
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
    ): void;
}
