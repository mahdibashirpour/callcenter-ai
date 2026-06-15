<?php

namespace App\Console\Commands;

use App\Services\ProcessingQueueFlusher;
use Illuminate\Console\Command;

class FlushProcessingQueueCommand extends Command
{
    protected $signature = 'processing-queue:flush
                            {--organization= : Limit to a specific organization ID}
                            {--all : Also delete completed/failed/cancelled history from the UI table}';

    protected $description = 'Clear Laravel processing jobs and sync the processing-queue UI state';

    public function handle(ProcessingQueueFlusher $flusher): int
    {
        $organizationId = $this->option('organization') !== null
            ? (int) $this->option('organization')
            : null;

        $result = $flusher->flush(
            organizationId: $organizationId,
            includeHistory: (bool) $this->option('all'),
        );

        $this->components->info(sprintf(
            'Processing queue flushed: %d Laravel job(s) deleted, %d failed job(s) cleared, %d UI job(s) cancelled.',
            $result['laravel_jobs_deleted'],
            $result['failed_jobs_deleted'],
            $result['tracking_jobs_cancelled'],
        ));

        return self::SUCCESS;
    }
}
