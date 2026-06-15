<?php

namespace App\Application\AiUsage\Listeners;

use App\Domain\Llm\Events\ConversationAnalyzed;
use App\Services\AiUsageAnalyticsService;
use Illuminate\Support\Facades\Log;

class RecordAiUsageSnapshot
{
    public function __construct(private AiUsageAnalyticsService $analytics) {}

    public function handle(ConversationAnalyzed $event): void
    {
        try {
            $this->analytics->recordAnalysis($event->result);
        } catch (\Throwable $e) {
            Log::warning('AI usage snapshot recording failed after analysis completed', [
                'analysis_id' => $event->analysisId,
                'call_id' => $event->result->callId,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
