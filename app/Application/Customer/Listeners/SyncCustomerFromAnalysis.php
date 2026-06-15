<?php

namespace App\Application\Customer\Listeners;

use App\Domain\Llm\Events\ConversationAnalyzed;
use App\Models\ConversationAnalysis;
use App\Services\CustomerIntelligenceService;

class SyncCustomerFromAnalysis
{
    public function __construct(
        private CustomerIntelligenceService $customers,
    ) {}

    public function handle(ConversationAnalyzed $event): void
    {
        $analysis = ConversationAnalysis::query()->find($event->analysisId);

        if (! $analysis) {
            return;
        }

        $this->customers->syncFromAnalysis($analysis);
    }
}
