<?php

namespace App\Console\Commands;

use App\Services\AiUsageAnalyticsService;
use Illuminate\Console\Command;

class RebuildAiUsageSnapshots extends Command
{
    protected $signature = 'ai:rebuild-usage-snapshots';

    protected $description = 'Rebuild AI usage daily snapshots from conversation analyses';

    public function handle(AiUsageAnalyticsService $analytics): int
    {
        $this->info('Rebuilding AI usage snapshots...');

        $count = $analytics->rebuildAllSnapshots();

        $this->info("Processed {$count} analyses.");

        return self::SUCCESS;
    }
}
