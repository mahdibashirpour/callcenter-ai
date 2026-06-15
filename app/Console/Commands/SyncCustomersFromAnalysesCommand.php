<?php

namespace App\Console\Commands;

use App\Models\ConversationAnalysis;
use App\Services\CustomerIntelligenceService;
use Illuminate\Console\Command;

class SyncCustomersFromAnalysesCommand extends Command
{
    protected $signature = 'customers:sync {--organization= : Limit to organization ID}';

    protected $description = 'Build or refresh customer records from existing conversation analyses';

    public function handle(CustomerIntelligenceService $service): int
    {
        $query = ConversationAnalysis::query()->orderBy('id');

        if ($organizationId = $this->option('organization')) {
            $query->where('organization_id', (int) $organizationId);
        }

        $count = 0;

        $query->chunkById(100, function ($analyses) use ($service, &$count): void {
            foreach ($analyses as $analysis) {
                if ($service->syncFromAnalysis($analysis)) {
                    $count++;
                }
            }
        });

        $this->info("Synced {$count} customer records.");

        return self::SUCCESS;
    }
}
