<?php

namespace App\Console\Commands;

use App\Services\RecordingRetentionService;
use Illuminate\Console\Command;

class PurgeExpiredRecordingsCommand extends Command
{
    protected $signature = 'recordings:purge-expired';

    protected $description = 'Delete audio recordings that exceeded the retention period';

    public function handle(RecordingRetentionService $retention): int
    {
        $purged = $retention->purgeDue();

        $this->components->info(sprintf(
            'Purged %d expired recording(s). Retention period: %d day(s).',
            $purged,
            $retention->retentionDays(),
        ));

        return self::SUCCESS;
    }
}
