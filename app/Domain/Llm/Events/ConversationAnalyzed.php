<?php

namespace App\Domain\Llm\Events;

use App\Domain\Llm\DTOs\AnalysisResultData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationAnalyzed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public int $analysisId,
        public AnalysisResultData $result,
    ) {}
}
