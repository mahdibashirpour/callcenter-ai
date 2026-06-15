<?php

namespace App\Domain\Llm\Contracts;

use App\Domain\Llm\DTOs\AnalysisResultData;

interface ConversationAnalysisRepositoryInterface
{
    public function store(AnalysisResultData $data): int;

    public function findLatestForCall(int $callId): ?AnalysisResultData;
}
